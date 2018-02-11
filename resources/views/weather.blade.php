<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>

        <title>Weather Forecast</title>
        <style>
            dt, dd { float: left }
            dt { clear:both }
        </style>
        <script>
            // accending sort
            function asc_sort(a, b){
                return ($(b).text()) < ($(a).text()) ? 1 : -1;
            }

            // decending sort
            function dec_sort(a, b){
                return ($(b).text()) > ($(a).text()) ? 1 : -1;
            }

            var sort_order = 'asc_sort';

            function favorite_sort() {
                if (sort_order == 'asc_sort') {
                    $(".favorites li").sort(asc_sort).appendTo('.favorites');
                } else {
                    $(".favorites li").sort(dec_sort).appendTo('.favorites');
                }
            }

            function setWeather(city) {
                $('#city').val(city);
                $('#getWeather').submit();
            }

            function changeSort() {
                sort_order = (sort_order == 'asc_sort') ? 'dec_sort' : 'asc_sort';
                favorite_sort();
            }

            function deleteFavorite(city, user_id) {
                $.post('/weather/deleteFavorite', {"city": city, "user_id": user_id, "_token": "{{ csrf_token() }}" },
                    function(data) {
                        location.reload();
                    }
                );
            }

            $(document).ready(function() {
                favorite_sort();

                $('#getWeather').submit(function() {
                    var city = $('#city').val();
                    if (!city) {
                        alert('Please enter a city.');
                        return false;
                    }
                    $.post('/weather', {"city": city, "_token": "{{ csrf_token() }}" }, function(data) {
                        $('#weather').show();
                        $('#temperature').html(data.weather.temperature);
                        $('#pressure').html(data.weather.pressure);
                        $('#humidity').html(data.weather.humidity);
                        $('#minTemp').html(data.weather.minTemp);
                        $('#maxTemp').html(data.weather.maxTemp);

                        $('#map').attr('src', '/weather/map/' + data.map.lat + '/' + data.map.lon);
                    });
                    return false;
                });

                $('#addFav').click(function() {
                    var city = $('#city').val();
                    var user_id = $('#user_id').val();

                    if (!city) {
                        alert('Please enter a city.');
                        return false;
                    }
                    $.post('/weather/favorite', {"city": city, "user_id": user_id, "_token": "{{ csrf_token() }}" }, function(data) {
                        if (data.success == true) {
                            $('.favorites').append('<li><a href="#" onclick="setWeather(\'' + city + '\')">' + city + '</a>' +
                                '<i class="fas fa-trash-alt" style="cursor: pointer" onclick="deleteFavorite(\'' + city + ', {{$id}}\')"></i></li>');
                            favorite_sort();
                            alert('Favorite added');
                        }
                    });
                });
            });
        </script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <h1>Weather Forecast</h1>

                    <form class="form-inline mb-2" id="getWeather">
                        <label for="city" class="control-label mr-2">City:</label>
                        <input type="text" name="city" id="city" class="form-control d-flex mr-2" placeholder="City Name" />
                        <input type="hidden" name="user_id" id="user_id" value="{{$id}}" />

                        <button class="btn btn-primary">Submit</button>
                    </form>

                    <div id="weather" style="display: none">
                        <dl class="list-inline">
                            <dt>Temperature</dt>
                            <dd id="temperature"></dd>
                            <dt>Pressure</dt>
                            <dd id="pressure"></dd>
                            <dt>Humidity</dt>
                            <dd id="humidity"></dd>
                            <dt>Max temperature</dt>
                            <dd id="maxTemp"></dd>
                            <dt>Min temperature</dt>
                            <dd id="minTemp"></dd>
                        </dl>
                        <div class="clearfix">
                            <button id="addFav" class="btn btn-info clearfix">Add to Favorites</button>
                        </div>
                    </div>

                    <div class="clearfix">
                        <iframe id="map" style="border: none; display: hidden; width: 100%; height: 400px;" class="clearfix"></iframe>
                    </div>


                </div>
                <ul class="col-md-4">
                    <h2>Favorites <i class="fas fa-sort" onclick="changeSort()" style="cursor: pointer"></i> </h2>
                    <ul class="favorites">
                        @foreach ($favorites as $favorite)
                            <li>
                                <a href="#" onclick="setWeather('{{$favorite->city_name}}')">{{$favorite->city_name}}</a>
                                <i class="fas fa-trash-alt" style="cursor: pointer" onclick="deleteFavorite('{{$favorite->city_name}}, {{$id}}')"></i>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>
