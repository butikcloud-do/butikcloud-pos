<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;


trait RecycleBinManager
{
    public function temporaryTrash($id)
    {

        $modelName = $this->getModelName();
        $item      = $this->getItem($id,  $modelName);
        $item->delete();

        $message = $modelName . " trashed successfully";

        adminActivity("$modelName-trash", "App\\Models\\$modelName", $id);
        return responseManager('trash', $message, 'success');
    }

    public function restoreTrash($id)
    {
        $modelName = $this->getModelName();
        $item      = $this->getItem($id, $modelName, 'onlyTrashed');
        $item->restore();

        $message = $modelName . " restored successfully";
        adminActivity("$modelName-restore", "App\\Models\\$modelName", $id);
        return responseManager('trash', $message, 'success');
    }


    private function getItem($id, $modelName, $methodName = "query")
    {

        $modelNameSpace = "App\\Models\\" . ucfirst($modelName);


        if (Schema::hasColumn((new $modelNameSpace)->getTable(), 'user_id')) {
            $item = $modelNameSpace::$methodName()
                ->where('id', $id)
                ->where('user_id', getParentUser()->id)
                ->firstOrFailWithApi($modelName);
        } else {
            $item = $modelNameSpace::$methodName()
                ->where('id', $id)
                ->firstOrFailWithApi($modelName);
        }



        return $item;
    }

    public function getModelName()
    {
        $selfClass = self::class;
        if (property_exists($selfClass, 'modelName')) return $this->modelName;
        $modelName = explode("\\", $selfClass);
        return str_replace("Controller", '', array_pop($modelName));
    }
}
