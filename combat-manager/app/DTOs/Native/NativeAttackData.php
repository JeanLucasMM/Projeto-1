<?php

namespace App\DTOs\Native;


class NativeAttackData
{

    public function __construct(

        public readonly string $id,

        public readonly string $title,

        public readonly string $mode,

        public readonly string $content,


        public readonly array $builder,


    ) {
    }



    public static function fromArray(array $data): self
    {

        return new self(

            id: (string) (
                $data['id'] ?? ''
            ),


            title: (string) (
                $data['title'] ?? ''
            ),


            mode: (string) (
                $data['mode'] ?? 'builder'
            ),


            content: (string) (
                $data['content'] ?? ''
            ),


            builder:
                $data['builder']
                ?? [],

        );

    }

}