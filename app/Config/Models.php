<?php
    // for alert foreach included
    foreach ($models as $model)
    { 
        if($model == null)
        {

        }else{
            include 'app/Models/'.$model.'.php'; 
            $object = new $model();
        }
    } 
?>