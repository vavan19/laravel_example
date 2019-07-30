<?php


Route::get('/excelparser', 'ExcelParsingController@index')->name('excel_parser.index');
Route::get('/excelparser/getexcelsheets', 'ExcelParsingController@getExcelSheets')->name('excel_parser.getexcelsheets');
Route::get('/excelparser/getexcelheader', 'ExcelParsingController@getExcelHeadersWithDatabaseFields')->name('excel_parser.getexcelheader');

Route::post('/excelparser/parse', 'ExcelParsingController@parseSheet')->name('excel_parser.parseParseSheet');

Route::post('/producer/update', 'ProducerController@update')->name('producer.update');
Route::get('/producer/getsheetsbyid', 'ProducerController@getSheetsById')->name('producer.getsheetsbyid');

Route::post('/sheet/upload', 'SheetsController@upload')->name('sheet.upload');

