<?php

return [

    'discount_per_item'     => 'Enable this if you want to add Discount to individual Invoice items. By default, Discount is added directly to the Invoice.',
    'tax_per_item'          => 'Enable this if you want to add Taxes to individual Invoice items. By default, taxes are added directly to the Invoice.',
    'add_first_customer_category' => 'Add your first customer category to organize your clients.',
    'add_first_buying_group' => 'Add your first buying group to manage customer groups.',
    'display_subaccount'    => 'Enable this if you want to display sub-account codes in Customer list table',
    'display_selling_prices' => 'Show additional selling price columns in order entry screens.',
    'display_cost_prices' => 'Show cost price information during order processing.',
    'order_customer_confirmation' => 'Automatically send order confirmation emails to customers.',
    'order_fulfillment_notification' => 'Send notifications to fulfillment team when orders are placed.',
    'fulfillment_mailbox_help' => 'Email address where order fulfillment notifications will be sent. You can add multiple email addresses separated by comma.',
    'add_first_tax_type' => 'Add your first tax type to get started.',
    'tax_percent_help' => 'Enter the tax rate as a percentage (e.g., 15 for 15%)',
    'tax_type_updated' => 'Tax type has been updated successfully.',
    'logo_requirements' => 'Recommended: Square image, at least 200x200px, PNG or JPG format.',

    // Messages related to the B2B shop
    'shop_welcome_message'  => 'Browse our products and discover competitive wholesale pricing. Login to access your negotiated rates.',
    'thanks_order'          => "Thank you for your order. We've received your order and will process it shortly.",
    'delivery_method_deliver' => "We'll deliver to your specified address",
    'delivery_method_pickup'  => "You will collect your order from our store",
    'delivery_method_schedule'=> 'Leave blank for standard delivery schedule',
    'delivery_address_update' => 'Update my default delivery address with these details',
    'collection_instructions' => 'Please bring a copy of your order confirmation, collection notification and valid ID when collecting your order.
                                  Large orders may require advance notice.',
    'credit_approval_required' => 'Orders requiring approval will be reviewed within 1 business day',
    'import_promotions'      => 'Import promotions from Excel/CSV files exported from your POS system',
    'enable_b2b_ecommerce' => 'Enable the B2B e-commerce portal for customer access.',
    'allow_guest_checkout' => 'Allow customers to place orders without creating an account.',
    'show_prices_without_login' => 'Display product prices to visitors who are not logged in.',
    'require_order_approval' => 'All orders require admin approval before processing.',
    'minimum_order_amount' => 'Set minimum order value requirement',
    'minimum_order_amount_help' => 'Minimum order value required for checkout. Set to 0 for no minimum.',
    'products_per_page' => 'Number of products to display per page',
    'products_per_page_help' => 'How many products to show on category and search pages (1-100).',
    'show_stock_levels' => 'Display available stock quantities to customers.',

    'location_code_help' => 'Use a unique 4-digit code (e.g., 0000, 0001, 0002). This should match your ERP system location codes.',
    'default_location_help' => 'Main location for inventory management. Used as fallback when no location is specified.',

    // Additional helpful messages for the location system:
    'sales_locations' => 'Enable multi-location inventory management. When disabled, all stock uses the default location (0000).',
    'manage_sales_locations' => 'Configure warehouse and store locations for multi-location inventory tracking.',
    'enable_locations_to_manage' => 'Enable sales locations in General Settings to start managing multiple warehouse locations.',
    'locations_disabled' => 'Multi-Location Management Disabled',
    'enable_locations' => 'Enable Locations',
    'add_first_location' => 'Click the Add Location button to create your first location.',

    // Location management messages
    'location_created_successfully' => 'Location ":name" has been created successfully.',
    'location_updated_successfully' => 'Location ":name" has been updated successfully.',
    'location_deleted_successfully' => 'Location ":name" has been deleted successfully.',
    'cannot_delete_default_location' => 'Cannot delete the default location. Please set another location as default first.',
    'cannot_delete_location_with_stock' => 'Cannot delete location with :count stock holdings. Please move or remove stock first.',
    'cannot_deactivate_default_location' => 'Cannot deactivate the default location. Please set another location as default first.',
    'location_status_updated' => 'Location status has been updated successfully.',
    'default_location_set' => ':name has been set as the default location.',
    'location_has_stock_holdings' => 'This location has :count stock holdings.',
    'location_code_change_warning' => 'Changing the location code may affect existing stock records and integrations.',
    'set_as_default_location_confirm' => 'Are you sure you want to set this as the default location? This will remove the default status from the current default location.',

    // Stock related messages
    'no_locations_found' => 'No locations configured yet.',
    'no_address_provided' => 'No address information provided.',
    'no_contact_provided' => 'No contact information provided.',
    'view_stock_holdings' => 'View Stock Holdings',
    'total_quantity' => 'Total Quantity',
    'products' => 'Products',

    // Validation messages
    'location_code_exists' => 'A location with this code already exists.',
    'location_code_max_length' => 'Location code cannot be longer than 10 characters.',
    'email_invalid' => 'Please enter a valid email address.',

    // General UI messages
    'basic_information' => 'Basic Information',
    'address_information' => 'Address Information',
    'contact_information' => 'Contact Information',
    'audit_information' => 'Audit Information',
    'stock_information' => 'Stock Information',
    'general_settings' => 'General Settings',
    'enable_sales_locations' => 'Enable Multi-Location Management',
    'default_location' => 'Default Location',
    'this_location' => 'this location',
    'are_you_sure' => 'Are you sure you want to',
    'error_loading_data' => 'Error loading data. Please try again.',
    'error_occurred' => 'An error occurred. Please try again.',
    'updating' => 'Updating',

    'main_currency_help' => 'Primary currency for all financial transactions.',
    'language_help' => 'Default language for the system interface.',
    'timezone_help' => 'Your local timezone for date and time display.',
    'date_format_help' => 'How dates will be displayed throughout the system.',
    'financial_year_starts' => 'The month your financial year begins.',
    'financial_year_ends' => 'The month your financial year ends.',
];
