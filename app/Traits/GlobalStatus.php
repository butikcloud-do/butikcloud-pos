<?php

namespace App\Traits;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait GlobalStatus
{
    public static function changeStatus($id, $column = 'status')
    {


        $modelName  = get_class();

        $query = $modelName::query();

        if (method_exists($modelName, "withTrashed")) {
            $query = $query->withTrashed();
        }

        if (isUserRequest() || isApiRequest()) {
            
            $query = $query->where('user_id', getParentUser()->id);
        }

        $query = $query->find($id);

        $modelParts = explode("\\", $modelName);
        $model      = strtolower(array_pop($modelParts));

        if (!$query) {
            $message = "The $model is not found";
            return responseManager('not_found', $message, 'error');
        }

        if ($query->$column == Status::ENABLE) {
            $query->$column = Status::DISABLE;
        } else {
            $query->$column = Status::ENABLE;
        }

        $message       = keyToTitle($column) . ' changed successfully';
        $query->save();

        adminActivity("$model-status-change", $modelName, $id);

        return responseManager('change_status', $message, 'success', [$model => $query]);
    }


    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn() => $this->badgeData(),
        );
    }

    public function badgeData()
    {
        $html = '';
        if ($this->status == Status::ENABLE) {
            $html = '<span class="badge   badge--success">' . trans('Enabled') . '</span>';
        } else {
            $html = '<span class="badge  badge--warning">' . trans('Disabled') . '</span>';
        }
        return $html;
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', Status::DISABLE);
    }
}
