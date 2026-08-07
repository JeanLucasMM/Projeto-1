<?php

namespace App\DTOs\Native;


class NativeMultiAttackData
{

    public function __construct(

        public readonly string $id,

        public readonly string $title,

        public readonly string $mode,

        public readonly string $customText,

        public readonly array $entries,

    ) {
    }



    public static function fromArray(array $data): self
    {

        return new self(

            id: (string)(
                $data['id']
                ?? ''
            ),


            title: (string)(
                $data['title']
                ?? 'Multiataque'
            ),


            mode: (string)(
                $data['mode']
                ?? 'automatic'
            ),


            customText: (string)(
                $data['customText']
                ?? ''
            ),


            entries:
                $data['entries']
                ?? [],

        );

    }

}