<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="utf-8" indent="yes" />
    <xsl:template match="/">
        <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
        <xsl:variable name="TypDziennika">
            <xsl:choose>
                <xsl:when test="//Dziennik/Swietlicy !=''">
                    <xsl:value-of select="'Dziennik świetlicy'"/>
                </xsl:when>
                <xsl:when test="//Dziennik/Przedszkola !=''">
                    <xsl:value-of select="'Dziennik przedszkola'"/>
                </xsl:when>
                <xsl:when test="//Dziennik/Szkoly !=''">
                    <xsl:value-of select="'Dziennik szkoły'"/>
                </xsl:when>
                <xsl:when test="//Dziennik/OsrodkaWychowawczego !=''">
                    <xsl:value-of select="'Dziennik ośrodka wychowawczego'"/>
                </xsl:when>
                <xsl:when test="//Dziennik/InnychZajec !=''">
                    <xsl:value-of select="'Dziennik innych zajęć'"/>
                </xsl:when>
                <xsl:when test="//Dziennik/InnychZajecPomocPsychologicznoPedagogiczna !=''">
                    <xsl:value-of select="'Dziennik zajęć pomocy psychologiczno pedagogicznej'"/>
                </xsl:when>
                <xsl:when test="//Dziennik/ZajecRewalidacyjnoWychowawczych !=''">
                    <xsl:value-of select="'Dziennik zajęć rewalidacyjno wychowawczych'"/>
                </xsl:when>
                <xsl:when test="//Dziennik/PedagogPsychologLogopeda !=''">
                    <xsl:value-of select="'Dziennik pedagoga/psychologa/logopedy'"/>
                </xsl:when>
                <xsl:when test="//Dziennik/ZajecStalych !=''">
                    <xsl:value-of select="'Dziennik zajęć stałych'"/>
                </xsl:when>
                <xsl:when test="//Dziennik/ZajecOkresowychLubOkazjonalnych !=''">
                    <xsl:value-of select="'Dziennik zajęć okresowych lub okazjonalnych'"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="'Dziennik'"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <html lang="pl">
            <head>
                <title>
                    <xsl:value-of select="$TypDziennika"/>
                </title>
                <style>
                    @page {
                        size: A4;
                        margin: 15mm 15mm 15mm 15mm;
                    }
                    body{
                        width: 745px;
                        /* to centre page on screen*/
                        margin-top: 0px;
                        margin-bottom: 0px;
                        margin-left: auto;
                        margin-right: auto;
                        font-size: 14px;
                        background-color: #FFFFFF;
                        font-family: 'lato' ;
                    }
                    strong{
                        font-size: 18px;
                    }
                    .text-center{
                        text-align: center;
                    }
                    .title{
                        font-size: 35px;
                    }
                    h1{
                        margin-top: 0.5em;
                        margin-bottom: 0.1em;
                    }
                    h2{
                        font-size: 26px;
                        margin-top: 0.1em;
                        margin-bottom: 0.5em;
                    }
                    h3{
                        font-size: 24px;
                        margin-top: 1em;
                        margin-bottom: 0.3em;
                    }
                    .front-page{
                        font-size: 14px;
                    }
                    table.bootstrap td{
                        height: 40px;
                        width: 8.333%;
                    }
                    table,td,th{
                        border: 2px solid #666;
                        border-collapse: collapse;
                        padding: 5px 4px;
                        font-size: 13px;
                    }

                    .no-border, .no-border td{
                        border: 0;
                    }
                    .thin-border, .thin-border td{
                        border: 2px solid #666;
                    }
                    .text-light{
                        display: block;
                        text-align: center;
                        font-size: 0.8em;
                    }
                    td {
                        text-align:center;
                        vertical-align:middle;
                        overflow: hidden;
                    }


                    .left{
                        text-align: left;
                    }

                    .font-large{
                        font-size: 16px;
                    }

                    .font-larger{
                        font-size: 20px;
                    }

                    .font-largerx{
                        font-size: 24px;
                    }

                    .font-largerxx{
                        font-size: 28px;
                    }

                    .vertical-text {
                        display: inline-block;
                        overflow: hidden;
                        width: 1.5em;
                    }
                    .vertical-text__inner {
                        display: inline-block;
                        white-space: nowrap;
                        line-height: 1.5;
                        filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
                        /*Firefox*/
                        -moz-transform: rotate(-90deg);
                        /*Safari*/
                        -webkit-transform: rotate(-90deg);
                        /*Opera*/
                        -o-transform: rotate(-90deg);
                        transform: translate(0,100%) rotate(-90deg);
                        transform-origin: 0 0;
                    }
                    .vertical-text__inner:after {
                        content: "";
                        display: block;
                        margin: -1.5em 0 100%;
                    }

                    td .vertical-text__inner{
                        white-space: nowrap;
                    }

                    /*pagebreaki*/
                    h3{
                        font-weight: normal;
                        page-break-before: left;
                    }
                    table {
                        width: 100%;
                    }
                    .width-20 {
                        width: 20%;
                    }
                    .width-10 {
                        width: 10%;
                    }
                </style>
            </head>
            <body>
                <div class="front-page">


                    <table  class="no-border bootstrap" >
                        <xsl:variable name="NazwaPlacowki" select="//NazwaPlacowki"/>
                        <xsl:if test="$NazwaPlacowki != ''">
                            <tr>
                                <td colspan="2">
                                    <strong><xsl:value-of select="$NazwaPlacowki"/></strong>
                                    <span class="text-light">
                                        (Nazwa placówki)
                                    </span>
                                    <br/>
                                </td>
                            </tr>
                        </xsl:if>
                        <xsl:variable name="AdresPlacowki" select="//AdresPlacowki"/>
                        <xsl:if test="$AdresPlacowki != ''">
                            <tr>
                                <td colspan="2" class="text-center">
                                    <strong><xsl:value-of select="//AdresPlacowki"/></strong>
                                    <span class="text-light">
                                        (Adres placówki)
                                    </span>
                                    <br/>
                                </td>
                            </tr>
                        </xsl:if>
                        <xsl:variable name="Wojewodztwo" select="//Wojewodztwo"/>
                        <xsl:if test="$Wojewodztwo != ''">
                            <tr>
                                <td >
                                    <strong><xsl:value-of select="//Wojewodztwo"/></strong>
                                    <span class="text-light">
                                        (województwo)
                                    </span>
                                </td>
                                <td >
                                    <strong><xsl:value-of select="//Gmina"/></strong>
                                    <span class="text-light">
                                        (Gmina/dzielnica)
                                    </span>
                                </td>
                            </tr>
                        </xsl:if>
                        <xsl:variable name="NazwaOddzialu" select="//NazwaOddzialu"/>
                        <xsl:variable name="NumerOddzialu" select="//NumerOddzialu"/>
                        <xsl:if test="$NazwaOddzialu != '' and $NumerOddzialu != ''">
                            <tr>
                                <td >
                                    <strong><xsl:value-of select="//NumerOddzialu"/></strong>
                                    <span class="text-light">
                                        (Numer oddziału)
                                    </span>
                                </td>
                                <td >
                                    <strong><xsl:value-of select="//NazwaOddzialu"/></strong>
                                    <span class="text-light">
                                        (Dane oddziału)
                                    </span>
                                </td>
                            </tr>
                        </xsl:if>
                        <tr>
                            <td colspan="2" class="text-center">
                                <h1 class="title">
                                    <xsl:value-of select="$TypDziennika"/>
                                </h1>
                            </td>
                        </tr>
                        <xsl:variable name="RokSzkolny" select="//RokSzkolny"/>
                        <xsl:if test="$RokSzkolny != ''">
                            <tr>
                                <td colspan="2" class="text-center">
                                    <h2>
                                        <xsl:value-of select="//RokSzkolny"/>
                                    </h2>
                                </td>
                            </tr>
                        </xsl:if>
                        <xsl:variable name="Wychowawca" select="//Wychowawca"/>
                        <xsl:if test="$Wychowawca != ''">
                            <tr>
                                <td colspan="2" >
                                    <strong><xsl:value-of select="//Wychowawca"/></strong>
                                    <span class="text-light">
                                        (wychowawca klasy)
                                    </span>

                                </td>
                            </tr>
                        </xsl:if>
                    </table>
                    <xsl:variable name="Uczniowie" select="//Uczniowie"/>
                    <xsl:if test="$Uczniowie != ''">
                        <h3>
                            <xsl:choose>
                                <xsl:when test="//Dziennik/Szkoly != ''">Dane osobowe uczniów klasy&#160;</xsl:when>
                                <xsl:when test="//Dziennik/Przedszkola != ''">Dane osobowe&#160;</xsl:when>
                            </xsl:choose>
                            <xsl:value-of select="//NumerOddzialu"/>&#160;<xsl:value-of select="//NazwaOddzialu"/>
                        </h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Numer</th>
                                <th>Imię i Nazwisko</th>
                                <xsl:if test="not(//Dziennik/Swietlicy != '') and not(//Dziennik/InnychZajec != '') and not(//Dziennik/InnychZajecPomocPsychologicznoPedagogiczna != '') and not(//Dziennik/PedagogPsychologLogopeda != '')">
                                    <th>Data urodzenia</th>
                                    <th>Miejsce urodzenia</th>
                                    <th>Adres zamieszkania</th>
                                    <th>Dane rodziców</th>
                                </xsl:if>
                                <xsl:if test="//Dziennik/Swietlicy != '' or //Dziennik/InnychZajecPomocPsychologicznoPedagogiczna != '' or //Dziennik/OsrodkaWychowawczego != ''">
                                    <th>Oddział</th>
                                </xsl:if>
                                <xsl:if test="//Dziennik/InnychZajecPomocPsychologicznoPedagogiczna != ''">
                                    <th>Rodzice</th>
                                </xsl:if>
                            </tr>
                            <xsl:for-each select="//Uczniowie/Uczen">
                                <tr>
                                    <td>
                                        <xsl:value-of select="numer"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="imie"/>&#160;<xsl:value-of select="nazwisko"/>
                                    </td>
                                    <xsl:if test="not(//Dziennik/Swietlicy != '') and not(//Dziennik/InnychZajec != '') and not(//Dziennik/InnychZajecPomocPsychologicznoPedagogiczna != '') and not(//Dziennik/PedagogPsychologLogopeda != '')">
                                    <td>
                                        <xsl:value-of select="dataUrodzenia"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="miejsceUrodzenia"/>
                                    </td>
                                    <td>
                                        <xsl:if test="adres/ulica != ''">ul.&#160;<xsl:value-of select="adres/ulica"/></xsl:if>&#160;<xsl:value-of select="adres/numerDomu"/><xsl:if test="adres/numerLokalu != ''">/<xsl:value-of select="adres/numerLokalu"/></xsl:if>&#160;<xsl:value-of select="adres/kodPocztowy"/>&#160;<xsl:value-of select="adres/miejscowosc"/>
                                    </td>
                                    <td>
                                        <xsl:for-each select="Rodzice/Rodzic">
                                        <xsl:value-of select="imie"/>&#160;<xsl:value-of
                                            select="nazwisko"/>
                                        <br/>
                                        <xsl:if test="adres/ulica != ''">ul.&#160;<xsl:value-of select="adres/ulica"/></xsl:if>&#160;<xsl:value-of select="adres/numerDomu"/>
                                        <xsl:if test="adres/numerLokalu != ''">/<xsl:value-of
                                                select="adres/numerLokalu"/>
                                        </xsl:if>&#160;<xsl:value-of select="adres/kodPocztowy"/>&#160;<xsl:value-of
                                            select="adres/miejscowosc"/>
                                        <br/>
                                        <xsl:value-of select="mail"/>
                                        <br/>
                                        <xsl:value-of select="telefon"/>
                                        <br />
                                        </xsl:for-each>
                                    </td>
                                    </xsl:if>
                                    <xsl:if test="//Dziennik/Swietlicy != '' or //Dziennik/InnychZajecPomocPsychologicznoPedagogiczna != '' or //Dziennik/OsrodkaWychowawczego != ''">
                                        <td>
                                            <xsl:value-of select="numerOddzialu"/>&#160;<xsl:value-of select="nazwaOddzialu"/>
                                        </td>
                                    </xsl:if>
                                    <xsl:if test="//Dziennik/InnychZajecPomocPsychologicznoPedagogiczna != ''">
                                        <td>
                                        <xsl:for-each select="Rodzice/Rodzic">
                                            <xsl:value-of select="mail"/>
                                            <br/>
                                            <xsl:value-of select="telefon"/>
                                            <br />
                                        </xsl:for-each>
                                        </td>
                                    </xsl:if>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="Nauczyciele" select="//Nauczyciele"/>
                    <xsl:if test="$Nauczyciele != ''">
                        <h3>Nauczyciele prowadzący zajęcia edukacyjne</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Imię i Nazwisko Nauczyciela</th>
                                <th>Numer</th>
                            </tr>
                            <xsl:for-each select="//Nauczyciele/Nauczyciel">
                                <tr>
                                    <td>
                                        <xsl:value-of select="imie"/>&#160;<xsl:value-of select="nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="numer"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="Przedmioty" select="//Przedmioty"/>
                    <xsl:if test="$Przedmioty != ''">
                        <h3>Zajęcia edukacyjne</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Nazwa</th>
                                <th>Numer</th>
                                <th>Skrót nazwy</th>
                                <th>Czy pozalekcyjne</th>
                            </tr>
                            <xsl:for-each select="//Przedmioty/Przedmiot">
                                <tr>
                                    <td>
                                        <xsl:value-of select="nazwa"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="numer"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="nazwaSkrocona"/>
                                    </td>
                                    <td>
                                        <xsl:choose>
                                            <xsl:when test="czyDodatkowy = 'true'">Tak</xsl:when>
                                            <xsl:when test="czyDodatkowy = 'false'">Nie</xsl:when>
                                        </xsl:choose>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="PlanyZajec" select="//PlanyZajec"/>
                    <xsl:if test="$PlanyZajec != ''">
                        <xsl:for-each select="//PlanyZajec/Plan">
                            <xsl:variable name="planZajec" select="."/>
                            <h3>Tygodniowy rozkład zajęć (od <xsl:value-of select="$planZajec/ObowiazujeOdDnia"/>)
                            </h3>
                            <table class="  bootstrap bottom-margin">
                                <tr>
                                    <th>Nr lekcji</th>
                                    <th>Godzina rozpoczęcia zajęć</th>
                                    <xsl:for-each select="//DniTygodnia/Dzien">
                                        <th class="width-10">
                                            <xsl:value-of select="nazwa"/>
                                        </th>
                                    </xsl:for-each>
                                </tr>
                                <xsl:for-each select="//GodzinyLekcyjne/Godzina">
                                    <xsl:variable name="idGodzinaLekcyjna" select="@id"/>
                                    <tr>
                                        <td>
                                            <xsl:value-of select="numer"/>
                                        </td>
                                        <td>
                                            <xsl:value-of select="czasOd"/>
                                        </td>
                                        <xsl:for-each select="//DniTygodnia/Dzien">
                                            <xsl:variable name="idDzienTygodnia" select="@id"/>
                                            <td>
                                                <xsl:for-each select="$planZajec/Wpis">
                                                    <xsl:variable name="plandzienTygodnia"
                                                                  select="DzienTygodniaRef/@id"/>
                                                    <xsl:variable name="planGodzinaLekcyjna"
                                                                  select="GodzinaLekcyjnaRef/@id"/>
                                                    <xsl:if test="$idDzienTygodnia = $plandzienTygodnia">
                                                        <xsl:if test="$idGodzinaLekcyjna = $planGodzinaLekcyjna">
                                                            <xsl:for-each select="Zajecia/Wpis">
                                                                <xsl:variable name="idPrzedmiotu"
                                                                              select="PrzedmiotRef/@id"/>
                                                                <xsl:value-of
                                                                        select="//Przedmioty/Przedmiot[@id=$idPrzedmiotu]/nazwa"/>
                                                                (<xsl:for-each select="Nauczyciele/NauczycielRef">
                                                                    <xsl:variable name="idNauczyciela"
                                                                                  select="@id"/> <xsl:value-of
                                                                            select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/imie"/>&#160;<xsl:value-of
                                                                        select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/nazwisko"/></xsl:for-each>)
                                                                <br/>
                                                            </xsl:for-each>
                                                        </xsl:if>
                                                    </xsl:if>
                                                </xsl:for-each>
                                            </td>
                                        </xsl:for-each>
                                    </tr>
                                </xsl:for-each>
                            </table>
                        </xsl:for-each>
                    </xsl:if>
                    <xsl:variable name="SemestralnyPlanZajec" select="//SemestralnyPlanZajec"/>
                    <xsl:if test="$SemestralnyPlanZajec != ''">
                        <h2>Semestralny plan zajęć</h2>
                        <xsl:for-each select="//SemestralnyPlanZajec/Plan">
                            <xsl:variable name="planZajec" select="."/>
                            <h3><xsl:value-of select="$planZajec/Data"/></h3>
                            <table class="  bootstrap bottom-margin">
                                <tr>
                                    <th>Nr lekcji</th>
                                    <th>Godzina rozpoczęcia zajęć</th>
                                    <th>Zajęcia</th>
                                </tr>
                                <xsl:for-each select="//GodzinyLekcyjne/Godzina">
                                    <xsl:variable name="idGodzinaLekcyjna" select="@id"/>
                                    <tr>
                                        <td>
                                            <xsl:value-of select="numer"/>
                                        </td>
                                        <td>
                                            <xsl:value-of select="czasOd"/>
                                        </td>
                                            <xsl:variable name="idDzienTygodnia" select="@id"/>
                                            <td>
                                                <xsl:for-each select="$planZajec/Wpis">

                                                    <xsl:variable name="planGodzinaLekcyjna"
                                                                  select="GodzinaLekcyjnaRef/@id"/>
                                                        <xsl:if test="$idGodzinaLekcyjna = $planGodzinaLekcyjna">
                                                            <xsl:for-each select="Zajecia/Wpis">
                                                                <xsl:variable name="idPrzedmiotu"
                                                                              select="PrzedmiotRef/@id"/>
                                                                <xsl:value-of
                                                                        select="//Przedmioty/Przedmiot[@id=$idPrzedmiotu]/nazwa"/>
                                                                (<xsl:for-each select="Nauczyciele/NauczycielRef">
                                                                <xsl:variable name="idNauczyciela"
                                                                              select="@id"/> <xsl:value-of
                                                                    select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/imie"/>&#160;<xsl:value-of
                                                                    select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/nazwisko"/></xsl:for-each>)
                                                                <br/>
                                                            </xsl:for-each>
                                                        </xsl:if>
                                                </xsl:for-each>
                                            </td>
                                    </tr>
                                </xsl:for-each>
                            </table>
                        </xsl:for-each>
                    </xsl:if>
                    <xsl:variable name="Tematy" select="//Tematy"/>
                    <xsl:if test="$Tematy != ''">
                        <h3>Tematy zajęć edukacyjnych</h3>
                        <table class="  bootstrap bottom-margin">
                            <tr>
                                <th class="width-20">Data</th>
                                <th class="width-20">Temat</th>
                                <th class="width-20">Zajęcia edukacyjne</th>
                                <th>Nr lekcji</th>
                                <th>Czas trwania realizacji</th>
                                <th>Od</th>
                                <th>Do</th>
                                <th>Nauczyciel prowadzący</th>
                            </tr>
                            <xsl:for-each select="//Tematy/Temat">
                                <xsl:variable name="idNauczyciela" select="nauczycielRef/@id"/>
                                <xsl:variable name="idPrzedmiotu" select="przedmiotRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="data"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="tresc"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="//Przedmioty/Przedmiot[@id=$idPrzedmiotu]/nazwa"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="numerLekcji"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="czasRealizacji"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="czasOd"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="czasDo"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/imie"/>&#160;<xsl:value-of
                                            select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/nazwisko"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="frekwencja" select="//Frekwencja"/>
                    <xsl:if test="$frekwencja != ''">
                        <h3>Frekwencja uczniów na zajęciach edukacyjnych</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Uczeń</th>
                                <th>Data</th>
                                <th>Godzina lekcyjna</th>
                                <th>Status</th>
                            </tr>
                            <xsl:for-each select="//Frekwencja/Wpis">
                                <xsl:variable name="idUcznia" select="uczenRef/@id"/>
                                <xsl:variable name="idGodzinyLekcyjnej" select="godzinaLekcyjnaRef/@id"/>
                                <xsl:variable name="idStatusuObecnosci" select="statusObecnosciRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="//Uczniowie/Uczen[@id=$idUcznia]/imie"/>&#160;<xsl:value-of
                                            select="//Uczniowie/Uczen[@id=$idUcznia]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="data"/>
                                    </td>
                                    <td>
                                        <xsl:value-of
                                                select="//GodzinyLekcyjne/Godzina[@id=$idGodzinyLekcyjnej]/numer"/>
                                        <br/>
                                        <xsl:value-of
                                                select="//GodzinyLekcyjne/Godzina[@id=$idGodzinyLekcyjnej]/czasOd"/>
                                    </td>
                                    <td>
                                        <xsl:value-of
                                                select="//StatusyObecnosci/Status[@id=$idStatusuObecnosci]/wartosc"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="OcenyOkresowe" select="//OcenyOkresowe"/>
                    <xsl:if test="$OcenyOkresowe != ''">
                        <h3>Oceny bieżące</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Uczeń</th>
                                <th>Zajęcia edukacyjne</th>
                                <th>Nauczyciel wystawiający</th>
                                <th>Okres</th>
                                <th>Ocena</th>
                                <th>Komentarz</th>
                            </tr>
                            <xsl:for-each select="//OcenyOkresowe/OcenaUcznia">
                                <xsl:variable name="idUcznia" select="UczenRef/@id"/>
                                <xsl:variable name="idNauczyciela" select="NauczycielRef/@id"/>
                                <xsl:variable name="idPrzedmiotu" select="PrzedmiotRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="//Uczniowie/Uczen[@id=$idUcznia]/imie"/>&#160;<xsl:value-of
                                            select="//Uczniowie/Uczen[@id=$idUcznia]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="//Przedmioty/Przedmiot[@id=$idPrzedmiotu]/nazwa"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/imie"/>&#160;<xsl:value-of
                                            select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="Okres"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="Ocena/prezentacja"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="Komentarz"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="OcenyKoncowe" select="//OcenyKoncowe"/>
                    <xsl:if test="$OcenyKoncowe != ''">
                        <h3>Zestawienie wyników klasyfikacji okresowej</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th rowspan="2">Nr</th>
                                <th rowspan="2">Imię i Nazwisko</th>
                                <th rowspan="2">Zachowanie</th>
                                <xsl:variable name="recordCount" select="count(//Przedmioty/Przedmiot)"/>
                                <th colspan="{$recordCount}">Nazwy Przedmiotów/Nauczyciele</th>
                            </tr>
                            <tr>
                                <xsl:for-each select="//Przedmioty/Przedmiot">
                                    <xsl:variable name="idNauczyciela" select="NauczycielRef/@id"/>
                                    <td>
                                        <div class="vertical-text">
                                            <div class="vertical-text__inner">
                                                <xsl:value-of select="nazwa"/>
                                                <xsl:value-of
                                                        select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/imie"/>&#160;<xsl:value-of
                                                    select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/nazwisko"/>
                                            </div>
                                        </div>
                                    </td>
                                </xsl:for-each>

                            </tr>
                            <xsl:for-each select="//Uczniowie/Uczen">
                                <xsl:variable name="idUcznia" select="@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="numer"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="imie"/>&#160;<xsl:value-of select="nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:for-each select="//OcenyKoncowe/OcenaKoncowa">
                                            <xsl:variable name="OcenaIdUcznia" select="UczenRef/@id"/>
                                            <xsl:if test="$idUcznia = $OcenaIdUcznia">
                                                <xsl:if test="czyZachowanie = 'true'">
                                                    <xsl:value-of select="Ocena/prezentacja"/>
                                                </xsl:if>
                                            </xsl:if>
                                        </xsl:for-each>&#160;
                                    </td>
                                    <xsl:for-each select="//Przedmioty/Przedmiot">
                                        <xsl:variable name="idPrzedmiotu" select="@id"/>
                                        <td>
                                            <xsl:for-each select="//OcenyKoncowe/OcenaKoncowa">
                                                <xsl:variable name="OcenaIddUcznia" select="UczenRef/@id"/>
                                                <xsl:variable name="OcenaIdPrzedmiotu" select="PrzedmiotRef/@id"/>
                                                <xsl:if test="$idUcznia = $OcenaIddUcznia">
                                                    <xsl:if test="$idPrzedmiotu = $OcenaIdPrzedmiotu">
                                                        <xsl:value-of select="Ocena/prezentacja"/>
                                                    </xsl:if>
                                                </xsl:if>
                                            </xsl:for-each>&#160;
                                        </td>
                                    </xsl:for-each>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="Egzaminy" select="//Egzaminy"/>
                    <xsl:if test="$Egzaminy != ''">
                        <h3>Egzaminy</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Uczeń</th>
                                <th>Zajęcia edukacyjne</th>
                                <th>Nauczyciel wystawiający</th>
                                <th>Okres</th>
                                <th>
                                    <div class="vertical-text">
                                        <div class="vertical-text__inner">
                                            Czy Egzamin poprawkowy
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="vertical-text">
                                        <div class="vertical-text__inner">
                                            Czy Egzamin ustny
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="vertical-text">
                                        <div class="vertical-text__inner">
                                            Czy Egzamin pisemny
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="vertical-text">
                                        <div class="vertical-text__inner">
                                            Czy Egzamin praktyczny
                                        </div>
                                    </div>
                                </th>
                                <th>Ocena</th>
                            </tr>
                            <xsl:for-each select="//Egzaminy/Egzamin">
                                <xsl:variable name="idUcznia" select="UczenRef/@id"/>
                                <xsl:variable name="idNauczyciela" select="NauczycielRef/@id"/>
                                <xsl:variable name="idPrzedmiotu" select="PrzedmiotRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="//Uczniowie/Uczen[@id=$idUcznia]/imie"/>&#160;<xsl:value-of
                                            select="//Uczniowie/Uczen[@id=$idUcznia]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="//Przedmioty/Przedmiot[@id=$idPrzedmiotu]/nazwa"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/imie"/>&#160;<xsl:value-of
                                            select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="Okres"/>
                                    </td>
                                    <td>
                                        <xsl:choose>
                                            <xsl:when test="czyPoprawkowy = 'true'">Tak</xsl:when>
                                            <xsl:otherwise>Nie</xsl:otherwise>
                                        </xsl:choose>
                                    </td>
                                    <td>
                                        <xsl:choose>
                                            <xsl:when test="czyUstny = 'true'">Tak</xsl:when>
                                            <xsl:otherwise>Nie</xsl:otherwise>
                                        </xsl:choose>
                                    </td>
                                    <td>
                                        <xsl:choose>
                                            <xsl:when test="czyPisemny = 'true'">Tak</xsl:when>
                                            <xsl:otherwise>Nie</xsl:otherwise>
                                        </xsl:choose>
                                    </td>
                                    <td>
                                        <xsl:choose>
                                            <xsl:when test="czyPraktyczny = 'true'">Tak</xsl:when>
                                            <xsl:otherwise>Nie</xsl:otherwise>
                                        </xsl:choose>
                                    </td>
                                    <td>
                                        <xsl:value-of select="Ocena/prezentacja"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="GodzinyPrzyprowadzania" select="//GodzinyPrzyprowadzania"/>
                    <xsl:if test="$GodzinyPrzyprowadzania != ''">
                        <h3>Godziny przyprowadzania</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Uczeń</th>
                                <th>Dzień tygodnia</th>
                                <th>Godzina przyprowadzania</th>
                                <th>Godzina odbierania</th>
                            </tr>
                            <xsl:for-each select="//GodzinyPrzyprowadzania/Wpis">
                                <xsl:variable name="idUcznia" select="uczenRef/@id"/>
                                <xsl:variable name="idDniaTygodnia" select="dzienTygodniaRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="//Uczniowie/Uczen[@id=$idUcznia]/imie"/>&#160;<xsl:value-of
                                            select="//Uczniowie/Uczen[@id=$idUcznia]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="//DniTygodnia/Dzien[@id=$idDniaTygodnia]/nazwa"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="godzinaPrzyprowadzania"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="godzinaOdbioru"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="PlanPracySwietlicy" select="//PlanPracySwietlicy"/>
                    <xsl:if test="$PlanPracySwietlicy != ''">
                        <h3>Plan pracy świetlicy</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Termin realizacji</th>
                                <th>Temat tygodnia</th>
                                <th>Cele dydaktyczno wychowawcze</th>
                                <th>Sposoby realizacji</th>
                            </tr>
                            <xsl:for-each select="//PlanPracySwietlicy/Wpis">
                                <tr>
                                    <td>
                                        <xsl:value-of select="TerminRealizacji"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="TematTygodnia"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="CeleDydaktycznoWychowawcze | CeleDydaktocznoWychowawcze"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="SposobyRealizacji"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="ObecnosciSwietlica" select="//ObecnosciSwietlica"/>
                    <xsl:if test="$ObecnosciSwietlica != ''">
                        <h3>Obecności</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Uczeń</th>
                                <th>Data</th>
                                <th>Status</th>
                            </tr>
                            <xsl:for-each select="//ObecnosciSwietlica/Wpis">
                                <xsl:variable name="idUcznia" select="uczenRef/@id"/>
                                <xsl:variable name="idStatusuObecnosci" select="statusObecnosciRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="//Uczniowie/Uczen[@id=$idUcznia]/imie"/>&#160;<xsl:value-of
                                            select="//Uczniowie/Uczen[@id=$idUcznia]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="data"/>
                                    </td>
                                    <td>
                                        <xsl:value-of
                                                select="//StatusyObecnosci/Status[@id=$idStatusuObecnosci]/wartosc"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="PlanPracyzWychowankami" select="//PlanPracyzWychowankami"/>
                    <xsl:if test="$PlanPracyzWychowankami != ''">
                        <h3>Plan pracy z wychowankami</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Działania wychowawcze</th>
                                <th>Formy realizacji</th>
                                <th>Odpowiedzialny</th>
                                <th>Termin realizacji</th>
                            </tr>
                            <xsl:for-each select="//PlanPracyzWychowankami/Wpis">
                                <tr>
                                    <td>
                                        <xsl:value-of select="dzialaniaWychowawcze"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="formyRealizacji"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="odpowiedzialny"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="terminRealizacji"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:if test="count(//Uczen[
                            liczbaGodzinNieobecnosciUsprawiedliwionych
                            or
                            liczbaGodzinNieobecnosciNieusprawiedliwionych
                        ]) &gt; 0">

                        <h3>Nieobecności wychowanków</h3>
                        <table class="thin-border bootstrap">
                            <tr>
                                <th>Numer</th>
                                <th>Imię i Nazwisko</th>
                                <th>Liczba godzin usprawiedliwionych</th>
                                <th>Liczba godzin nieusprawiedliwionych</th>
                            </tr>
                            <xsl:for-each select="//Uczen">
                                <xsl:variable name="usp" select="liczbaGodzinNieobecnosciUsprawiedliwionych"/>
                                <xsl:variable name="nieusp" select="liczbaGodzinNieobecnosciNieusprawiedliwionych"/>
                                <xsl:if test="$usp or $nieusp">
                                    <tr>
                                        <td><xsl:value-of select="numer"/></td>
                                        <td>
                                            <xsl:value-of select="imie"/>&#160;<xsl:value-of select="nazwisko"/>
                                        </td>
                                        <td>
                                            <xsl:choose>
                                                <xsl:when test="$usp"><xsl:value-of select="$usp"/></xsl:when>
                                                <xsl:otherwise>-</xsl:otherwise>
                                            </xsl:choose>
                                        </td>
                                        <td>
                                            <xsl:choose>
                                                <xsl:when test="$nieusp"><xsl:value-of select="$nieusp"/></xsl:when>
                                                <xsl:otherwise>-</xsl:otherwise>
                                            </xsl:choose>
                                        </td>
                                    </tr>
                                </xsl:if>
                            </xsl:for-each>
                        </table>
                    </xsl:if>

                    <xsl:variable name="UwagiOWychowankach" select="//UwagiOWychowankach"/>
                    <xsl:if test="$UwagiOWychowankach != ''">
                        <h3>Uwagi o wychowankach</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Uczeń</th>
                                <th>Uwaga</th>
                            </tr>
                            <xsl:for-each select="//UwagiOWychowankach/Wpis">
                                <xsl:variable name="idUcznia" select="uczenRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="//Uczniowie/Uczen[@id=$idUcznia]/imie"/>&#160;<xsl:value-of
                                            select="//Uczniowie/Uczen[@id=$idUcznia]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="uwaga"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="InformacjeOKontaktachZRodzinaWychowanka"
                                  select="//InformacjeOKontaktachZRodzinaWychowanka"/>
                    <xsl:if test="$InformacjeOKontaktachZRodzinaWychowanka != ''">
                        <h3>Informacje o kontaktach z rodziną wychowanka</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Uczeń</th>
                                <th>Data</th>
                                <th>Szczegóły</th>
                            </tr>
                            <xsl:for-each select="//InformacjeOKontaktachZRodzinaWychowanka/Wpis">
                                <xsl:variable name="idUcznia" select="uczenRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="//Uczniowie/Uczen[@id=$idUcznia]/imie"/>&#160;<xsl:value-of
                                            select="//Uczniowie/Uczen[@id=$idUcznia]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="data"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="szczegoly"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="IndywidualnyProgramPracyZDzieckiem"
                                  select="//IndywidualnyProgramPracyZDzieckiem"/>
                    <xsl:if test="$IndywidualnyProgramPracyZDzieckiem != ''">
                        <h3>Indywidualny program pracy z dzieckiem</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Uczeń</th>
                                <th>Szczegóły</th>
                            </tr>
                            <xsl:for-each select="//IndywidualnyProgramPracyZDzieckiem/Wpis">
                                <xsl:variable name="idUcznia" select="uczenRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="//Uczniowie/Uczen[@id=$idUcznia]/imie"/>&#160;<xsl:value-of
                                            select="//Uczniowie/Uczen[@id=$idUcznia]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="szczegoly"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="ProgramPracyGrupy" select="//ProgramPracyGrupy"/>
                    <xsl:if test="$ProgramPracyGrupy != ''">
                        <h3>Program pracy grupy</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Nazwa programu</th>
                                <th>Autor programu</th>
                            </tr>
                            <xsl:for-each select="//ProgramPracyGrupy/Wpis">
                                <tr>
                                    <td>
                                        <xsl:value-of select="nazwaProgramu"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="autorProgramu"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="OcenaPostepowIwnioski" select="//OcenaPostepowIwnioski"/>
                    <xsl:if test="$OcenaPostepowIwnioski != ''">
                        <h3>Ocena postępów i wnioski</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Uczeń</th>
                                <th>Ocena postępów</th>
                                <th>Wnioski</th>
                            </tr>
                            <xsl:for-each select="//OcenaPostepowIwnioski/Wpis">
                                <xsl:variable name="idUcznia" select="uczenRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="//Uczniowie/Uczen[@id=$idUcznia]/imie"/>&#160;<xsl:value-of
                                            select="//Uczniowie/Uczen[@id=$idUcznia]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="ocenaPostepow"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="wnioski"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="RozkladWlasnyZajec" select="//RozkladWlasnyZajec"/>
                    <xsl:if test="$RozkladWlasnyZajec != ''">
                        <xsl:for-each select="//RozkladWlasnyZajec/PlanZajec">
                            <xsl:variable name="planZajec" select="."/>
                            <h3>Rozkład własny zajęć (od <xsl:value-of select="$planZajec/obowiazujeOdDnia"/>)
                            </h3>
                            <table class=" bootstrap bottom-margin">
                                <tr>
                                    <th>Nr lekcji</th>
                                    <th>Godzina rozpoczęcia zajęć</th>
                                    <xsl:for-each select="//DniTygodnia/Dzien">
                                        <th class="width-10">
                                            <xsl:value-of select="nazwa"/>
                                        </th>
                                    </xsl:for-each>
                                </tr>
                                <xsl:for-each select="//GodzinyLekcyjne/Godzina">
                                    <xsl:variable name="idGodzinaLekcyjna" select="@id"/>
                                    <tr>
                                        <td>
                                            <xsl:value-of select="numer"/>
                                        </td>
                                        <td>
                                            <xsl:value-of select="czasOd"/>
                                        </td>
                                        <xsl:for-each select="//DniTygodnia/Dzien">
                                            <xsl:variable name="idDzienTygodnia" select="@id"/>
                                            <td>
                                                <xsl:for-each select="$planZajec/Wpis">
                                                    <xsl:variable name="plandzienTygodnia"
                                                                  select="dzienTygodniaRef/@id"/>
                                                    <xsl:variable name="planGodzinaLekcyjna"
                                                                  select="godzinaLekcyjnaRef/@id"/>
                                                    <xsl:if test="$idDzienTygodnia = $plandzienTygodnia">
                                                        <xsl:if test="$idGodzinaLekcyjna = $planGodzinaLekcyjna">
                                                            <xsl:value-of
                                                                    select="szczegoly"/>
                                                        </xsl:if>
                                                    </xsl:if>
                                                </xsl:for-each>
                                            </td>
                                        </xsl:for-each>
                                    </tr>
                                </xsl:for-each>
                            </table>
                        </xsl:for-each>
                    </xsl:if>
                    <xsl:variable name="ZajeciaICzynnosciPrzeprowadzoneWPoszczegolnychDniach"
                                  select="//ZajeciaICzynnosciPrzeprowadzoneWPoszczegolnychDniach"/>
                    <xsl:if test="$ZajeciaICzynnosciPrzeprowadzoneWPoszczegolnychDniach != ''">
                        <h3>Zajęcia i czynności przeprowadzone w poszczególnych dniach (w tym kontakty z instytucjami)</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Data</th>
                                <th>Uczeń</th>
                                <th>Szczegóły</th>
                                <th>Forma kontaktu</th>
                                <th>Instytucja</th>
                            </tr>
                            <xsl:for-each select="//ZajeciaICzynnosciPrzeprowadzoneWPoszczegolnychDniach/Wpis">
                                <xsl:variable name="idUcznia" select="uczenRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="data"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="//Uczniowie/Uczen[@id=$idUcznia]/imie"/>&#160;<xsl:value-of
                                            select="//Uczniowie/Uczen[@id=$idUcznia]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="szczegoly"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="formaKontaktu"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="nazwaInstytucji"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="ZalozeniaProgramowe" select="//ZalozeniaProgramowe"/>
                    <xsl:if test="$ZalozeniaProgramowe != ''">
                        <h3>Założenia programowe</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Nazwa programu</th>
                                <th>Autor</th>
                                <th>Data dopuszczenia</th>
                            </tr>
                            <xsl:for-each select="//ZalozeniaProgramowe/Wpis">
                                <tr>
                                    <td>
                                        <xsl:value-of select="nazwaProgramu"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="autorProgramu"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="dataDopuszczenia"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                    <xsl:variable name="TematyZLiczbaUczestnikow" select="//TematyZLiczbaUczestnikow"/>
                    <xsl:if test="$TematyZLiczbaUczestnikow != ''">
                        <h3>Tematy z liczbą uczestników</h3>
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th width="20%">Data</th>
                                <th width="20%">Temat</th>
                                <th width="20%">Zajęcia edukacyjne</th>
                                <th>Nr lekcji</th>
                                <th>Czas trwania realizacji</th>
                                <th>Od</th>
                                <th>Do</th>
                                <th>Nauczyciel prowadzący</th>
                                <th>Liczba uczestników</th>
                            </tr>
                            <xsl:for-each select="//TematyZLiczbaUczestnikow/Temat">
                                <xsl:variable name="idNauczyciela" select="nauczycielRef/@id"/>
                                <xsl:variable name="idPrzedmiotu" select="przedmiotRef/@id"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="data"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="tresc"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="//Przedmioty/Przedmiot[@id=$idPrzedmiotu]/nazwa"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="numerLekcji"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="czasRealizacji"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="czasOd"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="czasDo"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/imie"/>&#160;<xsl:value-of
                                            select="//Nauczyciele/Nauczyciel[@id=$idNauczyciela]/nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="liczbaUczestnikow"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
