<?php

use App\Odbc\Tps;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {

    $tps = new TPS;

    //Only Change This///////////
    //List of tables to convert//
    /////////////////////////////
    $tablesList = array(
        'Api_Pre_Bookings',
        'Parts',
        'Job_Control',
        //'Actions',
    );

    foreach ($tablesList as $table) {

        CreateTable($tps->readSchema("SELECT * FROM " . $table), $table);

        $tableData = $tps->read("SELECT * FROM " . $table);

        foreach ($tableData as $row) { //rows

            foreach ($row as $column => $value) { //columns

            }
        }

    }

});

/**
 * Create table schema
 */
function CreateTable($fieldTypesArr, $tableName)
{

    // foreach ($fieldTypesArr as $field) {
    //     foreach ($field as $name => $type) {
    //         echo $name . ' - ' . $type . '<br>';
    //     }
    // }

    // $ replaces with _
    // table names, field names lowercased
    // id and timestamsp fields added

    Schema::dropIfExists(strtolower($tableName));

    Schema::create(strtolower($tableName), function (Blueprint $table) use ($fieldTypesArr) {

        $table->bigIncrements('id');
        $table->timestamps();

        foreach ($fieldTypesArr as $field) {
            foreach ($field as $name => $type) {
                FieldBuilder($table, $type, strtolower(str_replace('$', '_', $name)));
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
            return $table->text($name);

    }

}
