<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gmopx\LaravelOWM\LaravelOWM;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use App\Favorite;


class WeatherController extends Controller
{
    public $weatherApi;

    public function __construct(
        LaravelOWM $weatherApi
    )
    {
        $this->weatherApi = $weatherApi;
    }

    public function index($id)
    {
        $favorites = Favorite::where('user_id', $id)->get();

        $data['id'] = $id;
        $data['favorites'] = $favorites;

        return view('weather', $data);
    }

    public function getWeather(Request $request) {
        $city = $request->input('city');

        $weather = $this->weatherApi->getCurrentWeather($city, 'en', 'metric');

        return response()->json([
            'weather' => [
                'temperature' => $weather->temperature->now->getFormatted(),
                'pressure' => $weather->pressure->getFormatted(),
                'humidity' => $weather->humidity->getFormatted(),
                'minTemp' => $weather->temperature->min->getFormatted(),
                'maxTemp' => $weather->temperature->max->getFormatted()
            ],
            'map' => [
                'lat' => $weather->city->lat,
                'lon' => $weather->city->lon
            ]
        ]);
    }

    public function getMap($lat, $lon)
    {
        Mapper::map($lat, $lon);

        return Mapper::render();
    }

    public function saveFavorite(Request $request)
    {
        $exists = Favorite::where('user_id', $request->input('user_id'))
            ->where('city_name', $request->input('city'))->count();

        $success = false;

        if (!$exists) {
            $favorite = new Favorite;
            $favorite->city_name = $request->input('city');
            $favorite->user_id = $request->input('user_id');

            $favorite->save();

            $success = true;
        }
        return response()->json(['success' => $success]);
    }

    public function deleteFavorite(Request $request)
    {
        $favorites = Favorite::where('user_id', $request->input('user_id'))
            ->where('city_name', $request->input('city'))->delete();
        return response()->json(['success' => true]);
    }
}
