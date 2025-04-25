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
            'pickup_time' => 'required|date_format:H:i', // Assumes H:i format from <input type="time">
            'eggs_ok' => ['required', Rule::in(['Yes', 'No'])], // Ensure value is Yes or No
            'allergies' => 'nullable|string',
            'cake_size' => 'required|string|max:50', // Max length for size description
            'cake_flavor' => 'required|string|max:255',
            'message_on_cake' => 'nullable|string|max:255',
            'custom_decoration' => 'nullable|string',
            'decoration_image' => 'nullable|image|mimes:jpeg,png,bmp,gif,svg,webp|max:2048', // Optional image, 2MB max
        ], [
            // Custom validation messages (optional)
            'pickup_date.after_or_equal' => 'The pickup date must be today or a future date.',
            'decoration_image.max' => 'The inspiration photo may not be larger than 2MB.',
            'decoration_image.image' => 'The inspiration photo must be an image file.',
            'decoration_image.mimes' => 'The inspiration photo must be a file of type: jpeg, png, bmp, gif, svg, webp.',
        ]);

        // Handle file upload if present
        $imagePath = null;
        if ($request->hasFile('decoration_image')) {
            $imagePath = $request->file('decoration_image')->store('decoration_images', 'public');
            // The path stored will be like 'decoration_images/filename.jpg' relative to the storage/app/public directory
        }

        // Add image path to validated data (if uploaded)
        $validatedData['decoration_image_path'] = $imagePath;

        $order = null; // Initialize order variable

        // Create and store the new order
        try {
            $order = CustomOrder::create($validatedData);

            // --- Send SMS Notifications --- 
            if ($order) { // Proceed only if order was created successfully
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

            // Redirect back with success message
            return redirect()->route('custom-order.create')->with('success', 'Thank you for your order request! We will text you shortly to confirm details and pricing.');

        } catch (\Exception $e) {
            // Log the error
            logger()->error("Error creating custom order: " . $e->getMessage());
            // Redirect back with an error message
            return redirect()->route('custom-order.create')->with('error', 'Sorry, there was an issue submitting your order. Please try again or contact us directly.')->withInput();
        }
    }
}
