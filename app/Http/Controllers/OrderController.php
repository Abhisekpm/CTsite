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

        // Create and store the new order
        try {
            CustomOrder::create($validatedData);

            // Redirect back with success message (actual message)
            return redirect()->route('custom-order.create')->with('success', 'Thank you for your order request! We will contact you shortly to confirm details and pricing.');

        } catch (\Exception $e) {
            // Log the error
            logger()->error("Error creating custom order: " . $e->getMessage());
            // Redirect back with an error message
            return redirect()->route('custom-order.create')->with('error', 'Sorry, there was an issue submitting your order. Please try again or contact us directly.')->withInput();
        }
    }
}
