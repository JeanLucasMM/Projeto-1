export default {

    exportNpc() {

        const clone = value => {

    if (value === null || value === undefined) {
        return value;
    }


    if (typeof value === "string" ||
        typeof value === "number" ||
        typeof value === "boolean") {

        return value;
    }


    if (Array.isArray(value)) {

        return value.map(item => clone(item));

    }


    if (typeof value === "object") {

        const result = {};


        for (const key in value) {


            // ignora objetos internos do editor
            if (
                key === "editorView" ||
                key === "pluginViews" ||
                key === "view" ||
                key === "editor"
            ) {
                continue;
            }


            result[key] = clone(value[key]);

        }


        return result;

    }


    return null;

};


        return {

            format: "npc-builder",

            version: 1,


            header: {

                name: this.header?.name ?? "",

                size: this.header?.size ?? "",

                types: clone(this.header?.types ?? []),

                alignments: clone(this.header?.alignments ?? []),

                languages: clone(this.header?.languages ?? []),

                languageCustom:
                    this.header?.languageCustom ?? "",

                challengeRating:
                    this.header?.challengeRating ?? "0",

            },


            combat: {

                ac_base:
                    this.combat?.ac_base ?? 10,

                ac_bonus:
                    this.combat?.ac_bonus ?? 0,

                ac_type:
                    this.combat?.ac_type ?? "",

                hp_mode:
                    this.combat?.hp_mode ?? "average",

                hit_dice_count:
                    this.combat?.hit_dice_count ?? 1,

                hit_die:
                    this.combat?.hit_die ?? "d8",

                hp_mod_extra:
                    this.combat?.hp_mod_extra ?? 0,

                custom_hp:
                    this.combat?.custom_hp ?? 0,


                senses:
                    clone(this.combat?.senses ?? {}),

                customSenses:
                    clone(this.combat?.customSenses ?? []),

                languages:
                    clone(this.combat?.languages ?? []),

                resistances:
                    clone(this.combat?.resistances ?? []),

                immunities:
                    clone(this.combat?.immunities ?? []),

                conditionImmunities:
                    clone(this.combat?.conditionImmunities ?? []),

                vulnerabilities:
                    clone(this.combat?.vulnerabilities ?? []),

            },


            speed: clone(this.speed ?? {}),


            abilities: clone(
                this.abilities ?? {}
            ),


            savingThrows: clone(
                this.savingThrows ?? {}
            ),


            skills: clone(
                this.skills ?? []
            ),



            sections: clone(
                this.sections ?? []
            ),



            attacks: (this.attacks ?? []).map(
                attack => ({

                    id: attack.id,

                    title:
                        attack.title ?? "",

                    mode:
                        attack.mode ?? "",

                    content:
                        typeof attack.content === "string"
                            ? attack.content
                            : "",


                    builder: {

                        targets:
                            clone(
                                attack.builder?.targets ?? []
                            ),

                        range:
                            clone(
                                attack.builder?.range ?? {}
                            ),

                        reach:
                            attack.builder?.reach ?? 0,


                        attackAbility:
                            attack.builder?.attackAbility ?? "",


                        proficiency:
                            attack.builder?.proficiency ?? false,


                        extraHitBonus:
                            attack.builder?.extraHitBonus ?? 0,


                        attackType:
                            attack.builder?.attackType ?? "",


                        damages:
                            clone(
                                attack.builder?.damages ?? []
                            ),


                        effects:
                            (attack.builder?.effects ?? [])
                            .map(effect => ({

                                id: effect.id,

                                content:
                                    typeof effect.content === "string"
                                        ? effect.content
                                        : "",

                            }))

                    }

                })
            ),



            multiAttacks:
                clone(this.multiAttacks ?? []),


            features:
                clone(this.features ?? []),


            actions:
                clone(this.actions ?? []),


            bonusActions:
                clone(this.bonusActions ?? []),


            reactions:
                clone(this.reactions ?? []),


            legendaryActions:
                clone(this.legendaryActions ?? []),


            lairActions:
                clone(this.lairActions ?? []),


            mythicActions:
                clone(this.mythicActions ?? []),

        };

    },



    downloadNpc() {

        const npc = this.exportNpc();


        const json = JSON.stringify(
            npc,
            null,
            4
        );


        const blob = new Blob(
            [json],
            {
                type: "application/json"
            }
        );


        const url =
            URL.createObjectURL(blob);



        const link =
            document.createElement("a");



        const name =
            (
                this.header?.name ||
                "npc"
            )
            .trim()
            .replace(/[\\/:*?"<>|]/g, "_");



        link.href = url;

        link.download =
            `${name}.json`;



        document.body.appendChild(link);

        link.click();

        document.body.removeChild(link);


        URL.revokeObjectURL(url);

    },


};