<?php

namespace App\Services\Interpreters\Native;


use App\DTOs\Native\NativeSpeedData;


class NativeSpeedInterpreter
{


    public function interpret(array $json): NativeSpeedData
    {

        $speed = $json['speed'] ?? [];


        return new NativeSpeedData(

            walk:
                (int)($speed['walk'] ?? 30),


            climb:
                (int)($speed['climb'] ?? 0),


            swim:
                (int)($speed['swim'] ?? 0),


            burrow:
                (int)($speed['burrow'] ?? 0),


            fly:
                (int)($speed['fly'] ?? 0),


            hover:
                (bool)($speed['hover'] ?? false),


            hasJumps:
                (bool)($speed['hasJumps'] ?? false),


            jumpHorizontalBonus:
                (int)($speed['jumpHorizontalBonus'] ?? 0),


            jumpVerticalBonus:
                (int)($speed['jumpVerticalBonus'] ?? 0),

        );

    }

}