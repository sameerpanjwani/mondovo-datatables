
# mondovo-datatables

Customized version of jQuery DataTables API for Laravel 5.4+

To initialize.
```
App::make(MyDataTable::class);
```

## Requirements

 - [PHP >= 7.0](http://php.net/)
 - [Laravel 5.4|5.5|5.6](https://github.com/laravel/framework)
 - [jQuery DataTables v1.10.x](http://datatables.net/)

## Documentations

-   [Laravel DataTables Documentation](http://yajrabox.com/docs/laravel-datatables)
-   [Laravel DataTables API](https://datatables.net/reference/api/)
-   [Laravel 5.4 Demo Application](http://dt54.yajrabox.com/)

## Laravel Version Compatibility
|Laravel| Package |
|--|--|
|5.4.x  | N/A |
|5.5.x  | N/A |
|5.6.x  | N/A |

#### Configuration (Required)

     php artisan vendor:publish --provider=Mondovo\DataTable\MondovoDataTableServiceProvider

And that's it! Start building out some awesome DataTables!

#### Set up
In composer.json add this repository

    "repositories": [  
        {  
            "type": "path",  
            "url": "https://github.com/sameerpanjwani/mondovo-datatables",  
            "options": {  
                "symlink": true  
		     }  
        }  
    ]

Run the following command

    composer require mondovo/datatable:"dev-master"

Add the following JS/CSS to the project

    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
    <script src="https://app.mondovo.io/plugins/common.js"></script>
    <script src="https://app.mondovo.io/themes/admin_templates/metronic_v3.7/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://app.mondovo.io/themes/admin_templates/metronic_v3.7/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js" type="text/javascript"></script>
    <script src="https://app.mondovo.io/themes/admin_templates/metronic_v3.7/assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.js" type="text/javascript"></script>
    <script src="https://www.mondovo.io/plugins/datatable-custom-filter.js"></script>
    <script src="https://www.mondovo.io/plugins/datatable-checkbox.js"></script>
    <script src="https://www.mondovo.io/js/text-selector-filter.js"></script>

    <link href="https://app.mondovo.io/themes/admin_templates/metronic_v3.7/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>  
    <link href="https://app.mondovo.io/themes/admin_templates/metronic_v3.7/assets/global/plugins/datatables/extensions/Responsive/css/dataTables.responsive.min.css" rel="stylesheet" type="text/css"/>  
    <link href="https://app.mondovo.io/themes/admin_templates/metronic_v3.7/assets/global/plugins/datatables/extensions/FixedColumns/css/dataTables.fixedColumns.min.css" rel="stylesheet" type="text/css"/>

Add a CSRF meta field

    <meta name="_token" content="{{ csrf_token() }}">

    $(function() {  
        $.ajaxSetup({  
            headers: {  
                'X-CSRF-Token': $('meta[name="_token"]').attr('content'),  
                'X-Requested-With': 'XMLHttpRequest'  
      }  
        });  
    });
