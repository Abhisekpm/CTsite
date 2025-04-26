<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Backend\Settings; // Add Settings model if needed for layout
use App\Models\Backend\Testimonial; // Import Testimonial model
use App\Models\Backend\MenuCategory; // Import MenuCategory model
use App\Models\Backend\Menu;         // Import Menu model
use Illuminate\Support\Facades\Validator; // Import Validator facade if needed for custom messages, though validate() is often sufficient
use Illuminate\Validation\Rule; // Import Rule for more complex rules like 'in'
use App\Models\CustomOrder; // Import the CustomOrder model
use Illuminate\Support\Facades\Storage; // Import Storage facade for file uploads
use Twilio\Rest\Client as TwilioClient; // Import Twilio Client
use Twilio\Exceptions\TwilioException; // Import Twilio Exception for specific catching
use Illuminate\Support\Facades\DB; // Import DB facade for potential transaction

class OrderController extends Controller
{
    /**
     * Show the form for creating a new custom cake order.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Fetch any data needed for the layout (like settings and testimonials)
        $settings = Settings::get()->first();
        $testimonials = Testimonial::where('status', 'active')->orderBy('created_at','desc')->get(); // Fetch active testimonials
        
        // Fetch cake flavors from the 'cakes' menu category (adjust 'cakes' slug if necessary)
        $cakeCategory = MenuCategory::where('slug', 'cakes')->first(); 
        $cakeFlavors = $cakeCategory 
                        ? $cakeCategory->menus()->where('status', 'active')->orderBy('name')->get() 
                        : collect(); // Provide an empty collection if category not found
        
        return view('custom_order', compact('settings', 'testimonials', 'cakeFlavors')); // Pass cakeFlavors to the view
    }

    /**
     * Store a newly created custom cake order request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Define validation rules
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|date_format:H:i',
            'eggs_ok' => ['required', Rule::in(['Yes', 'No'])],
            'allergies' => 'nullable|string',
            'cake_size' => 'required|string|max:50',
            'cake_flavor' => 'required|string|max:255',
            'message_on_cake' => 'nullable|string|max:255',
            'custom_decoration' => 'nullable|string',
            // Validation for multiple images
            'decoration_images' => 'nullable|array', // Must be an array if present
            'decoration_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120', // Each file: image, specific types, max 5MB
        ], [
            // Custom validation messages
            'pickup_date.after_or_equal' => 'The pickup date must be today or a future date.',
            'decoration_images.array' => 'Invalid image upload format.',
            'decoration_images.*.image' => 'One of the uploaded files is not a valid image.',
            'decoration_images.*.mimes' => 'Only JPG, PNG, GIF, SVG, WebP images are allowed.',
            'decoration_images.*.max' => 'One of the uploaded images exceeds the 5MB size limit.',
        ]);

        // Remove the old single image path key if it exists in validated data (it shouldn't, but just in case)
        unset($validatedData['decoration_image_path']); 
        unset($validatedData['decoration_images']); // We handle images separately after saving order

        $order = null; // Initialize order variable

        // Use a database transaction to ensure order and images are saved together or not at all
        DB::beginTransaction();

        try {
            // Create the order *without* image paths initially
            $order = CustomOrder::create($validatedData);

            // Handle multiple file uploads if present
            if ($request->hasFile('decoration_images')) {
                foreach ($request->file('decoration_images') as $file) {
                    // Store the file in 'public/custom_orders'
                    $path = $file->store('custom_orders', 'public');
                    
                    // Create associated image record
                    $order->images()->create(['path' => $path]);
                }
            }

            // --- Send SMS Notifications (Keep existing logic) --- 
            if ($order) { 
                try {
                    $twilioSid = env('TWILIO_SID');
                    $twilioToken = env('TWILIO_TOKEN');
                    $twilioFrom = env('TWILIO_FROM');
                    $adminPhone = env('ADMIN_PHONE');
                    $customerPhone = $order->phone; // Make sure phone number format matches Twilio requirements (E.164 recommended)

                    if ($twilioSid && $twilioToken && $twilioFrom && $adminPhone && $customerPhone) {
                        $twilio = new TwilioClient($twilioSid, $twilioToken);

                        // 1. Customer SMS (Pending)
                        $customerMessage = "Thanks, {$order->customer_name}! Your custom cake request (#{$order->id}) is received and pending review. We'll text you with pricing soon.";
                        $twilio->messages->create(
                            $customerPhone, // To customer
                            [
                                'from' => $twilioFrom,
                                'body' => $customerMessage
                            ]
                        );

                        // 2. Admin SMS (New Order)
                        $adminMessage = "New custom cake request (#{$order->id}) from {$order->customer_name} for pickup on {$order->pickup_date}. Needs pricing.";
                         $twilio->messages->create(
                            $adminPhone, // To admin
                            [
                                'from' => $twilioFrom,
                                'body' => $adminMessage
                            ]
                        );

                    } else {
                         logger()->warning('Twilio SMS not sent for order #' . $order->id . '. Missing Twilio/Admin config in .env');
                    }

                } catch (TwilioException $e) {
                    logger()->error('TwilioException sending SMS for order #' . ($order ? $order->id : 'N/A') . ': ' . $e->getMessage());
                    // Don't stop execution, but log the error
                } catch (\Exception $e) {
                    logger()->error('General Exception sending SMS for order #' . ($order ? $order->id : 'N/A') . ': ' . $e->getMessage());
                     // Don't stop execution, but log the error
                }
            }
            // --- End SMS Notifications --- 

            // If everything worked, commit the transaction
            DB::commit();

            // Redirect back with success message
            return redirect()->route('custom-order.create')->with('success', 'Thank you for your order request! We will text you shortly to confirm details and pricing.');

        } catch (\Exception $e) {
            // If any error occurred, roll back the transaction
            DB::rollBack();

            // Log the error
            logger()->error("Error creating custom order or saving images: " . $e->getMessage());
            
            // Optional: Clean up any potentially uploaded files if the transaction failed mid-way
            // (More complex logic, might depend on specific error handling needs)

            // Redirect back with an error message
            return redirect()->route('custom-order.create')->with('error', 'Sorry, there was an issue submitting your order. Please try again or contact us directly.')->withInput();
        }
    }
}
