@extends('layouts.app')

@section('title')
    Excel parser
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    {{ Form::label("producer_select", '1 Producer', ['class' => 'input-group-text']) }}
                </div>
                <select id="producer_select" class="custom-select" name="producer_select">
                    @foreach ($data['producers'] as $producer)
                        <option value="{{ $producer['id'] }}">{{$producer['title']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    {{ Form::label("sheet_template_select", '2 Template name', ['class' => 'input-group-text']) }}
                </div>

                <select id="sheet_template_select" class="custom-select" name="sheet_template_select">
                    @if ($data['producers']->first())
                        @foreach ($data['producers']->first()->sheets as $sheet)
                            <option value="{{ $sheet['id'] }}">{{$sheet['title']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="custom-file mb-3">
                {{ Form::open(array(
                    'method' => 'post',
                    'enctype' => "multipart/form-data",
                    'id' =>"excel_file_upload_form")
                ) }}

                    {{ Form::hidden(
                        'sheet_id',
                        $data['producers']->first() ?
                        $data['producers']->first()->sheets->first()['id'] :
                        "" ,
                        ['id'=> 'excel_file_sheet_id']
                    )
                    }}
                     {{ Form::hidden(
                        'sheet_name',
                        "" ,
                        ['id'=> 'sheet_name']
                    )
                    }}

                    {{Form::file("excel_file",
                        [
                            'id' => 'excel_file',
                            'class' => 'custom-select',
                            // 'accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel'
                        ]
                        )
                    }}

                    {{ Form::label("excel_file", '3 Choose excel file', ['class' => 'custom-file-label']) }}

                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    {{ Form::label("sheet_select", '4 Choose Excel sheet name', ['class' => 'input-group-text']) }}
                </div>
                <select id="sheet_select" class="custom-select" name="sheet_select">
                </select>
            </div>
        </div>
    </div>

    <div class="container">
        <h3>Match sheet columns with database</h3>

        <div class="container" id="messages">

        </div>


        <div class="row">
            <form id="columns_matcher_form">
                    {{ Form::hidden(
                        'sheet_id',
                        null,
                        ['id'=> 'sheet_id_input']
                    )
                    }}
                    {{ Form::hidden(
                        'sheet_name',
                        null,
                        ['id'=> 'parsing_sheet_name']
                    )
                    }}
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Product group</span>
                        </div>
                        <select id="product_group_id" class="custom-select" name="product_group_id">
                            @foreach ($data['product_groups'] as $product_group)
                                <option value="{{ $product_group['id'] }}">{{$product_group['title']}}</option>
                            @endforeach
                        </select>
                        {{-- <input type="text" name="product_group_id" class="form-control"> --}}
                    </div>

                    <div id="columns_matcher">

                    </div>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <button id="button_parse" type="button" class="btn btn-primary"
            style="display: none;">
                Parse Excel document
            </button>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script>
    $(function() {
        @if ($data['producers']->first())
            var sheet_id = '{{$data['producers']->first()->sheets->first()['id']}}';
        @else
            var sheet_id = '';
        @endif
        var excel_path = '';
        var excel_sheet = '';
        var excel_matching = '';
        var database_columns = '';
        var excel_columns = '';

        $('#producer_select').on('input',function(){
            producer_id = $("#producer_select option:selected").val() ;
            data = {
                'id' : producer_id,
            }
            $.ajax({
                url: '{{ route('producer.getsheetsbyid') }}',
                type: "GET",
                data: data,
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },success: function (data) {
                    // console.log(data['sheets']);
                    sheet_id = data['sheets'][0]['id']
                    var select = $("#sheet_template_select");
                    select.empty();
                    $.each(data['sheets'], function (index, item) {
                        if(index == 0){
                            $('#excel_file_sheet_id').val(item['id']);
                        }
                        $('<option></option>')
                                .attr('value', item['id'])
                                .text(item['title'])
                                .appendTo(select);

                    });

                },
                error: function (msg) {
                    console.log(msg);
                    AddAllert(msg['responseJSON']['message']);
                }
            });
        });

        $("#sheet_template_select").on('input',function(){
            sheet_id = $("#sheet_template_select option:selected").val() ;
            $('#sheet_id').val(sheet_id);
        });

        $('#excel_file').on('input',function(){
            console.log($('#excel_file')[0].files[0].name);
            $('#excel_file_upload_form label').text($('#excel_file')[0].files[0].name);
            file_upload_form = new FormData($("#excel_file_upload_form")[0]);
            // console.log
            $.ajax({
                url: '{{ route('sheet.upload') }}',
                type: "POST",
                processData: false,
                contentType: false,
                dataType: "json",
                data: file_upload_form,
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },success: function (data) {

                    console.log(data['sheet_path']);

                    excel_path = data['sheet_path'];
                    $.ajax({
                        url: '{{ route('excel_parser.getexcelsheets') }}',
                        type: "GET",
                        data: {
                            sheet_path: excel_path,
                            id        : sheet_id
                        },
                        headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },success: function (data) {
                            console.log(data);

                            var select = $("#sheet_select");
                            select.empty();
                            if( data['selected_sheet_mapping'] ){
                                $('<option></option>')
                                    .attr('value', data['selected_sheet_mapping'])
                                    .text(data['selected_sheet_mapping'])
                                    .appendTo(select);

                                CreateDatabaseColumnsAndSelects(data['selected_sheet_mapping']);
                            }else{
                                $('<option></option>')
                                    .attr('value', "")
                                    .text("")
                                    .appendTo(select);
                            }


                            $.each(data['excel_sheets'], function (index, item) {
                                $('<option></option>')
                                        .attr('value', item)
                                        .text(item)
                                        .appendTo(select);

                            });
                        },
                        error: function (msg) {
                            console.log(msg);
                            AddAllert(msg['responseJSON']['message']+'. Check your file format.');
                        }
                    });

                },
                error: function (msg) {
                    console.log(msg);
                    AddAllert(msg['responseJSON']['message']);
                }
            });
        });


        $('#sheet_select').on('input',function(){
            CreateDatabaseColumnsAndSelects($("#sheet_select option:selected").val());
        });

        function CreateDatabaseColumnsAndSelects(excel_sheet) {
            if(!excel_sheet)
                excel_sheet = $("#sheet_select option:selected").val() ;

            $('#sheet_name').val(excel_sheet);
            $('#parsing_sheet_name').val(excel_sheet);
            data = {
                'sheet_path': excel_path,
                'sheet_name': excel_sheet,
                'sheet_id': sheet_id,
            }
            $.ajax({
                url: '{{ route('excel_parser.getexcelheader') }}',
                type: "GET",
                data: data,
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },success: function (data) {
                    console.log(data);
                    database_columns = data['database_columns'];
                    excel_columns = data['excel_columns'];
                    mapping = data['mapping'];
                    var container = $("#columns_matcher");
                    container.empty();

                    $.each(excel_columns, function (index, item) {

                        database_column_select = $('<select></select>')
                        .attr('name', 'sheet_column['+index+']')
                        .attr('id', 'sheet_column['+index+']')
                        .attr('class', 'custom-select');
                        if(mapping != null && mapping[index] != null){
                            $('<option></option>')
                                .attr('value', mapping[index])
                                .text(mapping[index])
                                .appendTo(database_column_select);
                        }else{
                            $('<option></option>')
                                .attr('value', '')
                                .text('')
                                .appendTo(database_column_select);
                        }
                        $.each(database_columns, function(d_index, d_item){
                            $('<option></option>')
                                .attr('value', d_item)
                                .text(d_item)
                                .appendTo(database_column_select);
                        });

                        // to let user select a field to not be parsed
                        $('<option></option>')
                            .attr('value', '')
                            .text('')
                            .appendTo(database_column_select);

                        $('<div class="input-group"><div class="input-group-prepend"><span class="input-group-text" >'+item+'</span></div><select id="sheet_column['+index+']" name="sheet_column['+index+']" class="custom-select">'
                            + database_column_select.html()
                            +'</select></div>')
                        .appendTo(container);
                    });
                    $('#sheet_id_input').val(sheet_id);
                    $('#button_parse').show();
                },
                error: function (msg) {
                    console.log(msg);
                    AddAllert(msg['responseJSON']['message']);
                }
            });
        }

        $('#button_parse').on('click',function(){

            $.ajax({
                url: '{{ route('excel_parser.parseParseSheet') }}',
                type: "POST",
                processData: false,
                contentType: false,
                data:  new FormData($('#columns_matcher_form')[0]),
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },success: function (data) {
                    AddAllert(data['message'], 'success');
                },error: function (msg){
                    console.log(msg);
                    AddAllert(msg['responseJSON']['message']);
                }
            });
        })

        function AddAllert(message, type='') {
            if(type == 'success'){
                $('#messages').append('<div class="alert alert-success alert-dismissible fade show"><strong>Success! </strong> <span>'
                    +message
                    +'</span><button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            }else{
                $('#messages').append('<div class="alert alert-danger alert-dismissible fade show"><strong>Success! </strong> <span>'
                    +message
                    +'</span><button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            }
        }

    })

    </script>
@endsection
