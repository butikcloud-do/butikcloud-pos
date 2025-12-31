<?php

namespace App\Lib;

use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;

class DashBoardWidgetData
{
    /**
     * Retrieves all widget data by calling the corresponding methods
     *
     * @return array Combined array of widget data from various sources.
     */
    public static function getWidgetData(): array
    {
        // Initialize an empty array to hold the widget data
        $widget = [];

        // Loop through all methods in the class, excluding the 'getWidget' method itself
        foreach (get_class_methods(self::class) as $methodName) {
            if ($methodName == 'getWidgetData') {
                continue;
            }

            // Merge the results from each method into the widget array
            $widget = array_merge($widget, self::$methodName());
        }

        // Return the final combined widget data
        return $widget;
    }

    /**
     * Fetches sales data for various periods such as today, yesterday, this week, this month, and all-time sales.
     *
     * @return array Sales data, including total sales for each period.
     */
    public static function salesWidgetData(): array
    {
        $user = getParentUser();

        return Sale::where('user_id', $user->id)->selectRaw("
            COALESCE(SUM(CASE WHEN sale_date = ? THEN total END), 0) as today_sale,
            COALESCE(SUM(CASE WHEN sale_date = ? THEN total END), 0) as yesterday_sale,
            COALESCE(SUM(CASE WHEN sale_date >= ? THEN total END), 0) as this_week_sale,
            COALESCE(SUM(CASE WHEN sale_date >= ? THEN total END), 0) as this_month_sale,
            COALESCE(SUM(total), 0) as all_sale
        ", [
            now()->format("Y-m-d"),
            now()->subDay()->format("Y-m-d"),
            now()->startOfWeek()->format("Y-m-d"),
            now()->startOfMonth()->format("Y-m-d"),
        ])->first()->toArray();
    }

    /**
     * Fetches purchase data for various periods such as today, this week, this month, and all-time purchases.
     *
     * @return array Purchase data, including total purchases for each period.
     */
    public static function purchaseWidgetData(): array
    {
        $user = getParentUser();

        return Purchase::where('user_id', $user->id)->selectRaw("
            COALESCE(SUM(CASE WHEN purchase_date = ? THEN total END), 0) as today_purchase,
            COALESCE(SUM(CASE WHEN purchase_date >= ? THEN total END), 0) as this_week_purchase,
            COALESCE(SUM(CASE WHEN purchase_date >= ? THEN total END), 0) as this_month_purchase,
            COALESCE(SUM(total), 0) as all_purchase
        ", [
            now()->format("Y-m-d"),
            now()->startOfWeek()->format("Y-m-d"),
            now()->startOfMonth()->format("Y-m-d"),
        ])->first()->toArray();
    }

    /**
     * Fetches expense data for various periods such as today, this week, this month, and all-time expenses.
     *
     * @return array Expense data, including total expenses for each period.
     */
    public static function expenseWidgetData(): array
    {
        $user = getParentUser();

        return Expense::where('user_id', $user->id)->selectRaw("
            COALESCE(SUM(CASE WHEN expense_date = ? THEN amount END), 0) as today_expense,
            COALESCE(SUM(CASE WHEN expense_date >= ? THEN amount END), 0) as this_week_expense,
            COALESCE(SUM(CASE WHEN expense_date >= ? THEN amount END), 0) as this_month_expense,
            COALESCE(SUM(amount), 0) as all_expense
        ", [
            now()->format("Y-m-d"),
            now()->startOfWeek()->format("Y-m-d"),
            now()->startOfMonth()->format("Y-m-d"),
        ])->first()->toArray();
    }

    /**
     * Fetches balance data for various periods such as today, this week, this month, and all-time balance.
     * Balance is calculated as the sum of due_amount from purchases.
     *
     * @return array Balance data, including total balance for each period.
     */
    public static function balanceWidgetData(): array
    {
        $user = getParentUser();

        return Purchase::where('user_id', $user->id)->selectRaw("
            COALESCE(SUM(CASE WHEN purchase_date = ? THEN due_amount END), 0) as today_balance,
            COALESCE(SUM(CASE WHEN purchase_date >= ? THEN due_amount END), 0) as this_week_balance,
            COALESCE(SUM(CASE WHEN purchase_date >= ? THEN due_amount END), 0) as this_month_balance,
            COALESCE(SUM(due_amount), 0) as all_balance
        ", [
            now()->format("Y-m-d"),
            now()->startOfWeek()->format("Y-m-d"),
            now()->startOfMonth()->format("Y-m-d"),
        ])->first()->toArray();
    }

    /**
     * Fetches user data, including the total number of users, active users, email unverified users,
     * and mobile unverified users.
     *
     * @return array User data, including user counts for various statuses.
     */
    public static function userWidgetData(): array
    {
        return User::selectRaw("
            COUNT(*) as total_users,
            COUNT(CASE WHEN status = 1 THEN 1 END) as active_users,
            COUNT(CASE WHEN ev = 0 THEN 1 END) as email_unverified_users,
            COUNT(CASE WHEN sv = 0 THEN 1 END) as mobile_unverified_users
        ")->first()->toArray();
    }
}
