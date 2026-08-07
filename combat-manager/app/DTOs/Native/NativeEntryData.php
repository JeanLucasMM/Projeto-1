<?php

namespace App\DTOs\Native;


class NativeEntryData
{

    public function __construct(

        public readonly string $id,

        public readonly string $title,

        public readonly string $content,

        public readonly string $type,


        public readonly array $tracker,


        public readonly array $legendary,


        public readonly array $lair,


        public readonly array $spellcasting,


    ) {
    }


    public static function fromArray(array $data): self
    {
        return new self(

            id: (string) ($data['id'] ?? ''),

            title: (string) (
                $data['title']
                ?? ''
            ),

            content: (string) (
                $data['content']
                ?? ''
            ),

            type: (string) (
                $data['type']
                ?? 'normal'
            ),


            tracker:
                $data['tracker']
                ?? [],


            legendary:
                $data['legendary']
                ?? [],


            lair:
                $data['lair']
                ?? [],


            spellcasting:
                $data['spellcasting']
                ?? [],

        );
    }

}