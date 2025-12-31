<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $subjects = [
            ["name" => "Biologia", "shortcut" => "biol."],
            ["name" => "Biznes i zarządzanie", "shortcut" => "biz"],
            ["name" => "Chemia", "shortcut" => "chem."],
            ["name" => "Doradztwo zawodowe", "shortcut" => "dor. zaw."],
            ["name" => "Edukacja dla bezpieczeństwa", "shortcut" => "edb"],
            ["name" => "Edukacja informatyczna", "shortcut" => "ed. inf."],
            ["name" => "Edukacja matematyczna", "shortcut" => "ed. mat."],
            ["name" => "Edukacja muzyczna", "shortcut" => "ed. muz."],
            ["name" => "Edukacja obywatelska", "shortcut" => "eo"],
            ["name" => "Edukacja plastyczna", "shortcut" => "ed. plast."],
            ["name" => "Edukacja polonistyczna", "shortcut" => "ed. pol."],
            ["name" => "Edukacja przyrodnicza", "shortcut" => "ed. przyr."],
            ["name" => "Edukacja społeczna", "shortcut" => "ed. społ."],
            ["name" => "Edukacja techniczna", "shortcut" => "ed. techn."],
            ["name" => "Edukacja zdrowotna", "shortcut" => "ez"],
            ["name" => "Etyka", "shortcut" => "ety."],
            ["name" => "Filozofia", "shortcut" => "fil."],
            ["name" => "Fizyka", "shortcut" => "fiz."],
            ["name" => "Funkcjonowanie osobiste i społeczne", "shortcut" => "funkcj. osob. i społ."],
            ["name" => "Geografia", "shortcut" => "geogr."],
            ["name" => "Historia muzyki", "shortcut" => "hist. muzyki"],
            ["name" => "Historia sztuki", "shortcut" => "hist. sztuki"],
            ["name" => "Historia tańca", "shortcut" => "hist. tańca"],
            ["name" => "Historia", "shortcut" => "hist."],
            ["name" => "Informatyka", "shortcut" => "inf."],
            ["name" => "Język angielski", "shortcut" => "j. angielski"],
            ["name" => "Język francuski", "shortcut" => "j. francuski"],
            ["name" => "Język hiszpański", "shortcut" => "j. hiszpański"],
            ["name" => "Język łaciński i kultura antyczna", "shortcut" => "łac. i kult. ant."],
            ["name" => "Język łaciński", "shortcut" => "j. łaciński"],
            ["name" => "Język migowy", "shortcut" => "j. migowy"],
            ["name" => "Język niemiecki", "shortcut" => "j. niemiecki"],
            ["name" => "Język polski", "shortcut" => "j. polski"],
            ["name" => "Język rosyjski", "shortcut" => "j. rosyjski"],
            ["name" => "Język włoski", "shortcut" => "j. włoski"],
            ["name" => "Matematyka", "shortcut" => "mat."],
            ["name" => "Muzyka", "shortcut" => "muz."],
            ["name" => "Plastyka", "shortcut" => "plast."],
            ["name" => "Przyroda", "shortcut" => "przyr."],
            ["name" => "Religia", "shortcut" => "rel."],
            ["name" => "Technika", "shortcut" => "techn."],
            ["name" => "Wiedza o społeczeństwie", "shortcut" => "wos"],
            ["name" => "Wychowanie fizyczne", "shortcut" => "wf"],
            ["name" => "Zajęcia rozwijające komunikowanie się", "shortcut" => "zaj. rozwij. kom."],
            ["name" => "Zajęcia rozwijające kreatywność", "shortcut" => "zaj. rozwij. kreat."],
            ["name" => "Zajęcia z wychowawcą", "shortcut" => "zaj. z wych."],

        ];
        DB::table("subjects")->insert($subjects);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table("subjects")->where("*")->delete();
    }
};
