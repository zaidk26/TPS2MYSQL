<?php

use App\Odbc\Tps;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$tablesList = array(
    // "Stock_Movement_Header",
    // "Stock_Movement_Line",
    // "Stock_Movement_Serial",
    // "Stock_Take_Header",
    // "Stock_Take_Line",
    // "Stock_Take_Serial",
    // "Supplier_Credits",
    // "Symptoms",
    // "System_Parameters",
    // "Vision_Logs",
    // "Actions",
    // "API_Pre_Bookings",
    // "Claims_Log",
    // "Control",
    // "Customer_Branches",
    // "Customer_Comments",
    // "Customer_Walkin",
    // "Customers",
    // "DOA_Log",
    // "Exchange_Rates",
    // "Fault_Actions",
    // "Faults",
    // "Interfaces",
    // "Job_Accessories",
    // "Job_Accessories_Temp",
    // "Job_Action",
    "Job_Action_History",
    "Job_Comments",
    "Job_Comments_History",
    "Job_Control",
    "Job_Control_History",
    "Job_Log",
    "Job_Log_History",
    "Job_Photos",
    "Job_Scrapped_Devices",
    "Job_Shipping",
    "Job_Shipping_History",
    "Job_Spares",
    "Job_Spares_History",
    "Make_Model_Accessories",
    "Make_Models",
    "Make_Technician",
    "Makes",
    "Manifest_Header",
    "Manifest_Lines",
    "Model_Parts",
    "MTN_Jobs",
    "MTN_Logs",
    "Parts",
    "Parts_Serial_Tracking",
    "Payment_Types",
    "PostNet_Customers",
    "Pre_Bookings",
    "Requisition_Header",
    "Requisition_Line",
    "SMS_Log",
    "Status_Location",
);


//Build Tables
Route::get('/build', function () use($tablesList) {

    $tps = new TPS;
    
    foreach ($tablesList as $table) {

        CreateTable($tps->readSchema("SELECT * FROM {$table} "), $table);

    }

});


//Write Data to Tables
Route::get('/write', function () use($tablesList) {

    $tps = new TPS;

    foreach ($tablesList as $table) {

        $rows = $tps->read("SELECT * FROM " . $table);
       
        DB::table(strtolower($table))->truncate();

        foreach($rows as $row){
            DB::table(strtolower($table))->insert($row);
        }

       
    }    

});


/**
 * Create table schema
 */
function CreateTable($fieldTypesArr, $tableName)
{
    // $ replaces with _
    // table names, field names lowercased
    // id and timestamsp fields added

    Schema::dropIfExists(strtolower($tableName));

    Schema::create(strtolower($tableName), function (Blueprint $table) use ($fieldTypesArr) {

        $table->bigIncrements('id');
        $table->timestamps();

        foreach ($fieldTypesArr as $field) {
            foreach ($field as $name => $type) {
                FieldBuilder($table, $type, strtolower(str_replace('$', '_', $name)))->nullable();
            }
        }

    });

}

/**
 * Convert TopSpeed Field Types to Clarion
 * Map Any other types here
 */
function FieldBuilder($table, $type, $name)
{

    switch ($type) {

        case 'DATE':
            return $table->date($name);
            break;
        case 'TIME':
            return $table->time($name);
            break;
        case 'CHAR':
            return $table->string($name);
            break;
        case 'TINYINT':
            return $table->tinyInteger($name);
            break;
        case 'DECIMAL':
            return $table->decimal($name,10,2);
            break;
        case 'LONGVARCHAR':
            return $table->text($name);
            break;
        default:
        dd("TYPE NOT MAPPED , PLEASE MAP IN FIELD BUILDER FUNCTION " .$type);
           // return $table->text($name);

    }

}
