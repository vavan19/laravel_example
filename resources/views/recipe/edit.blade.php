@extends('layouts.app')

@section('title')
    Recipe
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div id="error" class="alert alert-danger alert-dismissible fade show" role="alert">
                <span class="msg"></span>
                <button type="button" class="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>


        <form>
            <div class="row align-items-center">
                <label for="recipe-id" class="mt-1 mr-5">Recipe ID</label>
                <input type="number" class="form-control w-auto mr-2" id="recipe-id" name="recipe-id">
                <button id="search" class="btn btn-default">Search</button>
            </div>

            <div class="row mt-3">
                <span class="switch">
                    <input type="checkbox" class="switch" name="breakfast" id="breakfast">
                    <label for="breakfast">Breakfast</label>
                </span>
            </div>

            <div class="row mt-2">
                <span class="switch">
                    <input type="checkbox" class="switch" name="dinner" id="dinner">
                    <label for="dinner">Dinner</label>
                </span>
            </div>

            <div class="row align-items-center mt-3">
                <label for="title" class="blue-label w-130">Title</label>
                <input type="text" class="form-control w-250 mr-2" id="title" name="title">
            </div>

            <div class="row align-items-center mt-2">
                <label for="servings" class="blue-label w-130">Servings</label>
                <input type="number" class="form-control w-250 mr-2" id="servings" name="servings">
            </div>

            <div class="row align-items-center mt-2">
                <label for="image" class="blue-label w-130">Image</label>
                <input type="text" class="form-control w-250 mr-2" id="image" name="image">
            </div>

            <div id="ingredients">
            </div>

            <div class="row mt-3">
                <label for="instructions" class="blue-label mb-2">Cooking instructions</label>
                <textarea class="form-control" name="instructions" id="instructions" rows="6"></textarea>
            </div>

            <div class="row align-items-center mt-3">
                <button id="save" class="btn btn-primary">Save</button>
                <div class="ml-3" id="save-status"></div>
            </div>

            <input type="hidden" id="loaded-id">
        </form>
    </div>

@endsection

@section('scripts')
    @parent

    <script>
        $error = $('#error');

        $error.find('.close').click(function () {
            $error.hide();
        });

        $(document).on("keydown", ":input:not(textarea)", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
            }
        });

        $('#dinner,#breakfast').click(function () {
            $('#save-status').text('Not saved').show();
        });

        $('textarea').keydown(function () {
            $('#save-status').text('Not saved').show();
        });

        $('body').on('keydown', 'input',function (event) {
            if ($(this).is('#recipe-id')) {
                if (event.key === "Enter") {
                    $('#search').click();
                }
            } else {
                if (event.key === "Enter") {
                    $('#save').click();
                } else {
                    $('#save-status').text('Not saved').show();
                }
            }
        });

        window.onbeforeunload = function (e) {
            e = e || window.event;

            // For IE and Firefox prior to version 4
            if (e) {
                e.returnValue = 'Are you sure that you want to leave this page?';
            }

            // For Safari
            return 'Are you sure that you want to leave this page?';
        };

        $('#search').click(function (e) {
            e.preventDefault();

            $error.hide();
            $('#save-status').text('');
            $('#save').hide();
            $('.saving').text('');
            $('#breakfast').prop('checked', false);
            $('#dinner').prop('checked', false);
            $('#ingredients').html('');
            $('#title').val('');
            $('#servings').val('');
            $('#image').val('');
            $('#instructions').val('');
            $('#loaded-id').val($('#recipe-id').val());

            $.ajax({
                url: '{{ route('recipe_search') }}',
                method: 'GET',
                data: {
                    id: $('#recipe-id').val()
                },
                dataType: 'json'
            }).done(function (response) {
                if (!response || !response.recipe) {
                    $error.find('.msg').text('This recipe does not exist');
                    $('#error').show();
                }

                if (response && response.recipe) {
                    $('#breakfast').prop('checked', response.recipe.breakfast);
                    $('#dinner').prop('checked', response.recipe.dinner);
                    $('#title').val(response.recipe.title);
                    $('#servings').val(response.recipe.servings);
                    $('#image').val(response.recipe.image_url);
                    $('#instructions').val(response.recipe.instruction);
                    $('#save').show();
                }

                if (response && response.ingredients) {
                    var ingredientsHtml = '';
                    var tableOpened = false;

                    for (var i in response.ingredients) {
                        var row = response.ingredients[i];
                        if (row.is_header) {
                            if (tableOpened) {
                                ingredientsHtml += '</tbody></table>';
                                tableOpened = false;
                            }

                            ingredientsHtml += '<div class="row align-items-center mt-2">' +
                                                '<label for="image" class="blue-label w-130">Section</label>' +
                                                '<input type="text" data-id="'+ row.id +'" class="form-control w-250 mr-2" value="'+ row.title +'" name="section">' +
                                            '</div>';
                        } else {
                            if (!tableOpened) {
                                ingredientsHtml += '<table class="ingredient-table mt-2">' +
                                                '<thead>' +
                                                    '<tr>' +
                                                    '<th><label>Ingredient title</label></th>' +
                                                    '<th><label>Amount</label></th>' +
                                                    '<th><label>Unit</label></th>' +
                                                    '<th><label>Grams</label></th>' +
                                                    '</tr>' +
                                                '</thead>' +
                                                '<tbody>';
                                tableOpened = true;
                            }

                            ingredientsHtml += '<tr data-id="'+ row.id +'">' +
                                    '<td><input type="text" value="'+ row.title +'"></td>' +
                                    '<td><input type="text" value="'+ row.amount +'"></td>' +
                                    '<td><input type="text" value="'+ row.unit +'"></td>' +
                                    '<td><input type="text" value="'+ row.grams +'"></td>' +
                                '</tr>';
                        }
                    }

                    if (tableOpened) {
                        ingredientsHtml += '</tbody></table>';
                    }

                    $('#ingredients').html(ingredientsHtml);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                $error.find('.msg').text(errorThrown);
                $error.show();
            });
        });

        $('#save').click(function (e) {
            e.preventDefault();
            save();
        });

        function save() {

            $('#save-status').text('Saving...').show();
            $error.hide();

            var ingredients = [];
            $('#ingredients .row input').each(function() {
                ingredients.push({
                    id: $(this).data('id'),
                    title: $(this).val(),
                    is_header: 1
                });
            });

            $('#ingredients tbody tr').each(function() {
                ingredients.push({
                    id: $(this).data('id'),
                    title: $(this).find('input').eq(0).val(),
                    is_header: 0,
                    amount: $(this).find('input').eq(1).val(),
                    unit: $(this).find('input').eq(2).val(),
                    grams: $(this).find('input').eq(3).val()
                });
            });

            $.ajax({
                url: '{{ url('/recipe') }}/' + $('#loaded-id').val(),
                method: 'POST',
                data: {
                    title: $('#title').val(),
                    servings: $('#servings').val(),
                    image: $('#image').val(),
                    breakfast: $('#breakfast').is(':checked') ? 1 : 0,
                    dinner: $('#dinner').is(':checked') ? 1 : 0,
                    instruction: $('#instructions').val(),
                    ingredients: ingredients
                },
                dataType: 'json'
            }).done(function () {
                $('#save-status').text('Saved');
            }).fail(function (jqXHR, textStatus, errorThrown) {
                $error.find('.msg').text(jqXHR.responseJSON.message);
                $error.show();
            });
        }
    </script>
@endsection
