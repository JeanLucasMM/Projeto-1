                {{-- FICHAS --}}
                <div
                    x-show="
                        drawer
                        ===
                        'sheets'
                    "
                >
                    <div
                        class="
                            mb-5
                            flex
                            items-center
                            justify-between
                            gap-3
                            rounded-xl
                            border
                            border-[#cdbb9f]/60
                            bg-[#fffdf8]
                            px-4
                            py-3
                        "
                    >
                        <div class="min-w-0">
                            <p
                                class="
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-[0.12em]
                                    text-[#8c6239]
                                "
                            >
                                Jogadores
                            </p>

                            <p
                                class="
                                    mt-0.5
                                    text-[11px]
                                    text-[#76553f]
                                "
                            >
                                Gerencie as fichas e convide jogadores para a campanha.
                            </p>
                        </div>

                        <button
                            type="button"
                            x-on:click="
                                closeDrawer();
                                inviteOpen = true;
                            "
                            class="
                                shrink-0
                                rounded-lg
                                bg-[#6b1d14]
                                px-3.5
                                py-2
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.08em]
                                text-[#fffaf2]
                                transition
                                hover:bg-[#53150f]
                            "
                        >
                            + Convidar
                        </button>
                    </div>

                    <div
                        class="
                            space-y-5
                        "
                    >
                        @foreach($campaign->members as $member)

                            @php
                                $memberCharacters =
                                    $campaign
                                        ->characters
                                        ->filter(
                                            fn ($character) =>
                                                (int) $character->user_id
                                                ===
                                                (int) $member->user_id
                                        )
                                        ->sortByDesc(
                                            fn ($character) =>
                                                (bool) $character->pivot->is_active
                                        )
                                        ->values();
                            @endphp

                            <section
                                x-data="{
                                    memberMenuOpen:
                                        false,
                                }"
                                x-on:click.outside="
                                    memberMenuOpen =
                                        false
                                "
                            >
                                <div
                                    class="
                                        mb-2
                                        flex
                                        items-center
                                        gap-2
                                    "
                                >
                                    <h3
                                        class="
                                            text-[9px]
                                            font-black
                                            uppercase
                                            tracking-[0.12em]
                                            text-[#53150f]
                                        "
                                    >
                                        {{ $member->user->name }}
                                    </h3>

                                    <span
                                        class="
                                            h-px
                                            flex-1
                                            bg-[#cdbb9f]/65
                                        "
                                    ></span>

                                    <span
                                        x-show="
                                            partyMemberHidden(
                                                {{ (int) $member->user_id }}
                                            )
                                        "
                                        x-cloak
                                        class="
                                            rounded-full
                                            border
                                            border-[#8c6239]/25
                                            bg-[#eadbc8]
                                            px-2
                                            py-1
                                            text-[7px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            text-[#6b1d14]
                                        "
                                    >
                                        Oculto
                                    </span>

                                    <span
                                        class="
                                            text-[8px]
                                            font-black
                                            text-[#8c6239]
                                        "
                                    >
                                        {{ $memberCharacters->count() }}
                                    </span>

                                    <div
                                        class="
                                            relative
                                            shrink-0
                                        "
                                    >
                                        <button
                                            type="button"
                                            x-on:click.stop="
                                                memberMenuOpen =
                                                    !memberMenuOpen
                                            "
                                            class="
                                                flex
                                                h-8
                                                w-8
                                                items-center
                                                justify-center
                                                rounded-lg
                                                border
                                                border-[#cdbb9f]/65
                                                bg-[#fffdf8]
                                                text-[14px]
                                                font-black
                                                tracking-[0.12em]
                                                text-[#6b1d14]
                                                transition
                                                hover:bg-[#eadbc8]
                                            "
                                            aria-label="Opções do Player"
                                            title="Opções"
                                        >
                                            ...
                                        </button>

                                        <div
                                            x-show="
                                                memberMenuOpen
                                            "
                                            x-cloak
                                            x-transition
                                            class="
                                                absolute
                                                right-0
                                                top-10
                                                z-30
                                                w-52
                                                overflow-hidden
                                                rounded-xl
                                                border
                                                border-[#cdbb9f]/70
                                                bg-[#fffdf8]
                                                p-1.5
                                                shadow-xl
                                            "
                                        >
                                            <button
                                                type="button"
                                                x-on:click="
                                                    setPartyMemberVisibility(
                                                        {{ (int) $member->user_id }},
                                                        !partyMemberHidden(
                                                            {{ (int) $member->user_id }}
                                                        ),
                                                        @js($member->user->name)
                                                    );

                                                    memberMenuOpen =
                                                        false;
                                                "
                                                :disabled="
                                                    memberVisibilityUserId
                                                    !==
                                                    null
                                                "
                                                class="
                                                    flex
                                                    w-full
                                                    items-center
                                                    justify-between
                                                    gap-3
                                                    rounded-lg
                                                    px-3
                                                    py-2.5
                                                    text-left
                                                    text-[10px]
                                                    font-black
                                                    text-[#6b1d14]
                                                    transition
                                                    hover:bg-[#f2eadf]
                                                    disabled:cursor-wait
                                                    disabled:opacity-50
                                                "
                                            >
                                                <span
                                                    x-text="
                                                        partyMemberHidden(
                                                            {{ (int) $member->user_id }}
                                                        )
                                                            ? 'Mostrar para a Party'
                                                            : 'Esconder da Party'
                                                    "
                                                ></span>

                                                <span
                                                    class="
                                                        text-[11px]
                                                        text-[#9a806d]
                                                    "
                                                    x-text="
                                                        partyMemberHidden(
                                                            {{ (int) $member->user_id }}
                                                        )
                                                            ? '◉'
                                                            : '◌'
                                                    "
                                                ></span>
                                            </button>

                                            <div
                                                class="
                                                    my-1
                                                    h-px
                                                    bg-[#eadbc8]
                                                "
                                            ></div>

                                            <button
                                                type="button"
                                                x-on:click="
                                                    memberMenuOpen =
                                                        false;

                                                    removePartyMember({
                                                        user_id:
                                                            {{ (int) $member->user_id }},

                                                        player_name:
                                                            @js($member->user->name),
                                                    });
                                                "
                                                :disabled="
                                                    removingMemberUserId
                                                    !==
                                                    null
                                                "
                                                class="
                                                    flex
                                                    w-full
                                                    items-center
                                                    justify-between
                                                    gap-3
                                                    rounded-lg
                                                    px-3
                                                    py-2.5
                                                    text-left
                                                    text-[10px]
                                                    font-black
                                                    text-red-800
                                                    transition
                                                    hover:bg-red-50
                                                    disabled:cursor-wait
                                                    disabled:opacity-50
                                                "
                                            >
                                                <span>
                                                    Excluir da Party
                                                </span>

                                                <span
                                                    class="
                                                        text-[12px]
                                                        text-red-500
                                                    "
                                                >
                                                    ×
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="
                                        grid
                                        gap-2
                                        sm:grid-cols-2
                                    "
                                >
                                    @forelse($memberCharacters as $character)
                                        <a
                                            href="{{ route('characters.show', $character) }}"
                                            class="
                                                flex
                                                items-center
                                                gap-3
                                                rounded-xl
                                                border
                                                p-3
                                                transition
                                                {{
                                                    $character->pivot->is_active
                                                        ? 'border-[#b08c62]/60 bg-[#fffdf8] shadow-sm'
                                                        : 'border-[#d8cec0]/60 bg-[#eee9e1] opacity-60 grayscale-[40%] hover:opacity-95 hover:grayscale-0'
                                                }}
                                            "
                                        >
                                            <div
                                                class="
                                                    flex
                                                    h-12
                                                    w-12
                                                    shrink-0
                                                    items-center
                                                    justify-center
                                                    overflow-hidden
                                                    rounded-lg
                                                    border
                                                    border-[#cdbb9f]
                                                    bg-[#eadbc8]
                                                    font-serif
                                                    text-lg
                                                    font-black
                                                    text-[#53150f]
                                                "
                                            >
                                                @if($character->image_path)
                                                    @php
                                                        $campaignCharacterImagePath =
                                                            ltrim(
                                                                str_replace(
                                                                    '\\',
                                                                    '/',
                                                                    trim($character->image_path)
                                                                ),
                                                                '/'
                                                            );

                                                        foreach (
                                                            [
                                                                'storage/',
                                                                'public/',
                                                            ]
                                                            as $campaignImagePrefix
                                                        ) {
                                                            if (
                                                                str_starts_with(
                                                                    $campaignCharacterImagePath,
                                                                    $campaignImagePrefix
                                                                )
                                                            ) {
                                                                $campaignCharacterImagePath =
                                                                    substr(
                                                                        $campaignCharacterImagePath,
                                                                        strlen($campaignImagePrefix)
                                                                    );
                                                            }
                                                        }

                                                        $campaignCharacterImageUrl =
                                                            '/storage/'
                                                            .
                                                            implode(
                                                                '/',
                                                                array_map(
                                                                    'rawurlencode',
                                                                    explode(
                                                                        '/',
                                                                        $campaignCharacterImagePath
                                                                    )
                                                                )
                                                            );
                                                    @endphp

                                                    <img
                                                        src="{{ $campaignCharacterImageUrl }}"
                                                        alt=""
                                                        class="
                                                            h-full
                                                            w-full
                                                            object-cover
                                                            object-top
                                                        "
                                                    >
                                                @else
                                                    {{
                                                        mb_strtoupper(
                                                            mb_substr(
                                                                $character->name,
                                                                0,
                                                                1
                                                            )
                                                        )
                                                    }}
                                                @endif
                                            </div>

                                            <div
                                                class="
                                                    min-w-0
                                                    flex-1
                                                "
                                            >
                                                <p
                                                    class="
                                                        truncate
                                                        font-serif
                                                        text-[13px]
                                                        font-black
                                                        text-[#53150f]
                                                    "
                                                >
                                                    {{ $character->name }}
                                                </p>

                                                <p
                                                    class="
                                                        mt-0.5
                                                        text-[7px]
                                                        font-black
                                                        uppercase
                                                        tracking-wider
                                                        {{
                                                            $character->pivot->is_active
                                                                ? 'text-emerald-800'
                                                                : 'text-[#8c6239]'
                                                        }}
                                                    "
                                                >
                                                    {{
                                                        $character->pivot->is_active
                                                            ? 'Jogando agora'
                                                            : 'Descansando'
                                                    }}
                                                </p>
                                            </div>
                                        </a>
                                    @empty
                                        <p
                                            class="
                                                col-span-full
                                                rounded-xl
                                                border
                                                border-dashed
                                                border-[#cdbb9f]
                                                px-4
                                                py-6
                                                text-center
                                                text-[9px]
                                                text-[#8c6239]
                                            "
                                        >
                                            Nenhuma ficha compartilhada.
                                        </p>
                                    @endforelse
                                </div>
                            </section>
                        @endforeach
                    </div>
                </div>


