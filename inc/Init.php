<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc;

final class Init
{
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public static function getServices()
    {
        return [
            Pages\Dashboard::class,
            Base\Enqueue::class,
            Pages\Settings::class,
            Pages\Shortcodes::class,
            Base\SettingsLinks::class,
            Base\AddressController::class,
            Base\AuthenticationController::class,
            Base\NinjaFormsController::class,
            Base\PaymentController::class,
            Base\RegistrationController::class,
            Base\TeachersWidgetController::class,
            Base\ShortcodesHelpersController::class,
            Base\ShortcodesAddressesController::class,
            Base\ShortcodesCoursesController::class,
            Base\ShortcodesOpenItemsController::class,
            Base\ShortcodesPaymentsController::class,
            Base\ShortcodesRegistrationsController::class,
            Base\ShortcodesSubscriptionsController::class,
        ];
    }

    /**
     * Loop through the classes, initialize them,
     * and call the register() method if it exists
     * @return
     */
    public static function registerServices()
    {
        foreach (self::getServices() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Initialize the class
     * @param class $class class from the services array
     * @return class instance  new instance of the class
     */
    private static function instantiate($class)
    {
        $service = new $class();

        return $service;
    }
}
