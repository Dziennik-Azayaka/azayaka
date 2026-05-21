<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="utf-8" indent="yes" />
    <xsl:template match="/">
        <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
        <xsl:variable name="TypKsiegi">
            <xsl:choose>
                <xsl:when test="//Ksiega/EwidencjiDzieci !=''">
                    <xsl:value-of select="'Księga ewidencji dzieci podlegających obowiązkowi rocznego przygotowania przedszkolnego  i obowiązkowi szkolnemu zamieszkałych w obwodzie szkoły'"/>
                </xsl:when>
                <xsl:when test="//Ksiega/Uczniow !=''">
                    <xsl:value-of select="'Księga uczniów'"/>
                </xsl:when>
                <xsl:when test="//Ksiega/Sluchaczy !=''">
                    <xsl:value-of select="'Księga słuchaczy'"/>
                </xsl:when>
                <xsl:when test="//Ksiega/Wychowankow !=''">
                    <xsl:value-of select="'Księga wychowanków'"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="'Księga'"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <html lang="pl">
            <head>
                <title>
                    <xsl:value-of select="$TypKsiegi"/>
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
                                        (Województwo)
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
                        <xsl:variable name="DataZalozeniaEwidencji" select="//DataZalozeniaEwidencji"/>
                        <xsl:if test="$DataZalozeniaEwidencji != ''">
                            <tr>
                                <td >
                                </td>
                                <td >
                                    <strong><xsl:value-of select="//DataZalozeniaEwidencji"/></strong>
                                    <span class="text-light">
                                        (Data założenia ewidencji)
                                    </span>
                                </td>
                            </tr>
                        </xsl:if>
                        <tr>
                            <td colspan="2" class="text-center">
                                <h1 class="title">
                                    <xsl:value-of select="$TypKsiegi"/>
                                </h1>
                            </td>
                        </tr>
                        <xsl:variable name="RokSzkolny" select="//DataZalozeniaEwidencji"/>
                    </table>

                    <xsl:variable name="Dzieci" select="//Dzieci"/>
                    <xsl:if test="$Dzieci != ''">
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Numer</th>
                                <th>Imię (imiona) i nazwisko dziecka</th>
                                <th>Data i miejsce urodzenia dziecka</th>
                                <th>PESEL dziecka</th>
                                <th>Adres zamieszkania dziecka</th>
                                <th>Imiona i nazwiska rodziców (prawnych opiekunów), adresy ich zamieszkania</th>
                            </tr>
                            <xsl:for-each select="//Dzieci/Dziecko">
                                <tr>
                                    <td rowspan="2">
                                        <xsl:value-of select="Numer"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="Imie"/>&#160;<xsl:value-of select="Nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="DataUrodzenia"/><br /><xsl:value-of select="MiejsceUrodzenia"/>
                                    </td>
                                    <td>
                                        <xsl:if test="Pesel != ''">
                                            <xsl:value-of select="Pesel"/>
                                        </xsl:if>
                                        <xsl:if test="NazwaINumerDokumentuPotwierdzajacegoTozsamosc != ''">
                                            <xsl:value-of select="NazwaINumerDokumentuPotwierdzajacegoTozsamosc"/>
                                        </xsl:if>
                                    </td>
                                    <td>
                                        <xsl:if test="Adres/Ulica != ''">ul.&#160;<xsl:value-of select="Adres/Ulica"/></xsl:if>&#160;<xsl:value-of select="Adres/NumerDomu"/><xsl:if test="Adres/NumerLokalu != ''">/<xsl:value-of select="Adres/NumerLokalu"/></xsl:if><br /><xsl:value-of select="Adres/KodPocztowy"/>&#160;<xsl:value-of select="Adres/Miejscowosc"/>
                                    </td>
                                    <td>
                                        <xsl:for-each select="Rodzice/Rodzic">
                                        <xsl:value-of select="Imie"/>&#160;<xsl:value-of
                                            select="Nazwisko"/>
                                        <br/>
                                        <xsl:if test="Adres/Ulica != ''">ul.&#160;<xsl:value-of select="Adres/Ulica"/></xsl:if>&#160;<xsl:value-of select="Adres/NumerDomu"/>
                                        <xsl:if test="Adres/NumerLokalu != ''">/<xsl:value-of
                                                select="Adres/NumerLokalu"/>
                                        </xsl:if><br /><xsl:value-of select="Adres/KodPocztowy"/>&#160;<xsl:value-of
                                            select="Adres/Miejscowosc"/><br />
                                        </xsl:for-each>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="left">
                                        <xsl:variable name="InformacjeOPrzedszkolu" select="InformacjeOPrzedszkolu"/>
                                        <xsl:if test="$InformacjeOPrzedszkolu != ''">
                                            <b>Informacje o przedszkolu lub innej formie wychowania przedszkolnego:</b><br />
                                            <xsl:value-of select="InformacjeOPrzedszkolu"/><br /><br />
                                        </xsl:if>
                                        <xsl:variable name="InformacjeOOdroczeniu" select="InformacjeOOdroczeniu"/>
                                        <xsl:if test="$InformacjeOOdroczeniu != ''">
                                            <b>Informacje o odroczeniu rozpoczęcia spełniania przez dziecko obowiązku szkolnego</b><br />
                                            <xsl:value-of select="InformacjeOOdroczeniu"/><br /><br />
                                        </xsl:if>
                                        <xsl:variable name="InformacjeOSzkoleLubMiejscuRealizacjiZajecRewalidacyjnoWychowawczych" select="InformacjeOSzkoleLubMiejscuRealizacjiZajecRewalidacyjnoWychowawczych"/>
                                        <xsl:if test="$InformacjeOSzkoleLubMiejscuRealizacjiZajecRewalidacyjnoWychowawczych != ''">
                                            <b>Informacje o szkole, w tym o szkole za granicą lub przy przedstawicielstwie dyplomatycznym innego państwa w Polsce, albo miejscu realizacji zajęć rewalidacyjno-wychowawczych:</b><br />
                                            <xsl:value-of select="InformacjeOSzkoleLubMiejscuRealizacjiZajecRewalidacyjnoWychowawczych"/><br /><br />
                                        </xsl:if>
                                        <xsl:variable name="InformacjeOSpelnianiuPrzezDzieckoObowiazkuSzkolnegoPozaSzkola" select="InformacjeOSpelnianiuPrzezDzieckoObowiazkuSzkolnegoPozaSzkola"/>
                                        <xsl:if test="$InformacjeOSpelnianiuPrzezDzieckoObowiazkuSzkolnegoPozaSzkola != ''">
                                            <b>Informacje o spełnianiu przez dziecko obowiązku szkolnego poza szkołą:</b><br />
                                            <xsl:value-of select="InformacjeOSpelnianiuPrzezDzieckoObowiazkuSzkolnegoPozaSzkola"/><br /><br />
                                        </xsl:if>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>

                    <xsl:variable name="Uczniowie" select="//Uczniowie"/>
                    <xsl:if test="$Uczniowie != ''">
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Numer</th>
                                <th>Imię (imiona) i nazwisko ucznia</th>
                                <th>Data i miejsce urodzenia ucznia</th>
                                <th>PESEL ucznia</th>
                                <th>Adres zamieszkania ucznia</th>
                                <th>Imiona i nazwiska rodziców (prawnych opiekunów), adresy ich zamieszkania</th>
                                <th>Data rozpoczęcia nauki</th>
                                <th>Oddział</th>
                                <th>Data ukończenia szkoły</th>
                                <th>Data i przyczyna opuszczenia szkoły</th>
                            </tr>
                            <xsl:for-each select="//Uczniowie/Uczen">
                                <tr>
                                    <td>
                                        <xsl:value-of select="Numer"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="Imie"/>&#160;<xsl:value-of select="Nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="DataUrodzenia"/><br /><xsl:value-of select="MiejsceUrodzenia"/>
                                    </td>
                                    <td>
                                        <xsl:if test="Pesel != ''">
                                            <xsl:value-of select="Pesel"/>
                                        </xsl:if>
                                        <xsl:if test="NazwaINumerDokumentuPotwierdzajacegoTozsamosc != ''">
                                            <xsl:value-of select="NazwaINumerDokumentuPotwierdzajacegoTozsamosc"/>
                                        </xsl:if>
                                    </td>
                                    <td>
                                        <xsl:if test="Adres/Ulica != ''">ul.&#160;<xsl:value-of select="Adres/Ulica"/></xsl:if>&#160;<xsl:value-of select="Adres/NumerDomu"/><xsl:if test="Adres/NumerLokalu != ''">/<xsl:value-of select="Adres/NumerLokalu"/></xsl:if><br /><xsl:value-of select="Adres/KodPocztowy"/>&#160;<xsl:value-of select="Adres/Miejscowosc"/>
                                    </td>
                                    <td>
                                        <xsl:for-each select="Rodzice/Rodzic">
                                            <xsl:value-of select="Imie"/>&#160;<xsl:value-of
                                                select="Nazwisko"/>
                                            <br/>
                                            <xsl:if test="Adres/Ulica != ''">ul.&#160;<xsl:value-of select="Adres/Ulica"/></xsl:if>&#160;<xsl:value-of select="Adres/NumerDomu"/>
                                            <xsl:if test="Adres/NumerLokalu != ''">/<xsl:value-of
                                                    select="Adres/NumerLokalu"/>
                                            </xsl:if><br /><xsl:value-of select="Adres/KodPocztowy"/>&#160;<xsl:value-of
                                                select="Adres/Miejscowosc"/><br />
                                        </xsl:for-each>
                                    </td>
                                    <td><xsl:value-of select="DataRozpoczeciaNauki"/></td>
                                    <td><xsl:value-of select="Oddzial"/></td>
                                    <td><xsl:value-of select="DataUkonczeniaSzkoly"/></td>
                                    <td><xsl:value-of select="DataIPrzyczynaOpuszczeniaSzkoly"/></td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>

                    <xsl:variable name="Sluchacze" select="//Sluchacze"/>
                    <xsl:if test="$Sluchacze != ''">
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Numer</th>
                                <th>Imię (imiona) i nazwisko ucznia</th>
                                <th>Data urodzenia i miejsce urodzenia ucznia</th>
                                <th>PESEL ucznia</th>
                                <th>Adres zamieszkania ucznia</th>
                                <th>Data rozpoczęcia nauki</th>
                                <th>Oddział</th>
                                <th>Data ukończenia szkoły</th>
                                <th>Data i przyczyna opuszczenia szkoły</th>
                            </tr>
                            <xsl:for-each select="//Sluchacze/Sluchacz">
                                <tr>
                                    <td>
                                        <xsl:value-of select="Numer"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="Imie"/>&#160;<xsl:value-of select="Nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="DataUrodzenia"/><br /><xsl:value-of select="MiejsceUrodzenia"/>
                                    </td>
                                    <td>
                                        <xsl:if test="Pesel != ''">
                                            <xsl:value-of select="Pesel"/>
                                        </xsl:if>
                                        <xsl:if test="NazwaINumerDokumentuPotwierdzajacegoTozsamosc != ''">
                                            <xsl:value-of select="NazwaINumerDokumentuPotwierdzajacegoTozsamosc"/>
                                        </xsl:if>
                                    </td>
                                    <td>
                                        <xsl:if test="Adres/Ulica != ''">ul.&#160;<xsl:value-of select="Adres/Ulica"/></xsl:if>&#160;<xsl:value-of select="Adres/NumerDomu"/><xsl:if test="Adres/NumerLokalu != ''">/<xsl:value-of select="Adres/NumerLokalu"/></xsl:if><br /><xsl:value-of select="Adres/KodPocztowy"/>&#160;<xsl:value-of select="Adres/Miejscowosc"/>
                                    </td>
                                    <td><xsl:value-of select="DataRozpoczeciaNauki"/></td>
                                    <td><xsl:value-of select="Oddzial"/></td>
                                    <td><xsl:value-of select="DataUkonczeniaSzkoly"/></td>
                                    <td><xsl:value-of select="DataIPrzyczynaOpuszczeniaSzkoly"/></td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>

                    <xsl:variable name="Wychowankowie" select="//Wychowankowie"/>
                    <xsl:if test="$Wychowankowie != ''">
                        <table  class="thin-border bootstrap" >
                            <tr>
                                <th>Numer</th>
                                <th>Imię (imiona) i nazwisko wychowanka</th>
                                <th>Data i miejsce urodzenia wychowanka</th>
                                <th>PESEL wychowanka</th>
                                <th>Adres zamieszkania wychowanka</th>
                                <th>Imiona i nazwiska rodziców (prawnych opiekunów), adresy ich zamieszkania</th>
                                <th>Data przyjęcia wychowanka</th>
                            </tr>
                            <xsl:for-each select="//Wychowankowie/Wychowanek">
                                <tr>
                                    <td rowspan="2">
                                        <xsl:value-of select="Numer"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="Imie"/>&#160;<xsl:value-of select="Nazwisko"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="DataUrodzenia"/><br /><xsl:value-of select="MiejsceUrodzenia"/>
                                    </td>
                                    <td>
                                        <xsl:if test="Pesel != ''">
                                            <xsl:value-of select="Pesel"/>
                                        </xsl:if>
                                        <xsl:if test="NazwaINumerDokumentuPotwierdzajacegoTozsamosc != ''">
                                            <xsl:value-of select="NazwaINumerDokumentuPotwierdzajacegoTozsamosc"/>
                                        </xsl:if>
                                    </td>
                                    <td>
                                        <xsl:if test="Adres/Ulica != ''">ul.&#160;<xsl:value-of select="Adres/Ulica"/></xsl:if>&#160;<xsl:value-of select="Adres/NumerDomu"/><xsl:if test="Adres/NumerLokalu != ''">/<xsl:value-of select="Adres/NumerLokalu"/></xsl:if><br /><xsl:value-of select="Adres/KodPocztowy"/>&#160;<xsl:value-of select="Adres/Miejscowosc"/>
                                    </td>
                                    <td>
                                        <xsl:for-each select="Rodzice/Rodzic">
                                            <xsl:value-of select="Imie"/>&#160;<xsl:value-of
                                                select="Nazwisko"/>
                                            <br/>
                                            <xsl:if test="Adres/Ulica != ''">ul.&#160;<xsl:value-of select="Adres/Ulica"/></xsl:if>&#160;<xsl:value-of select="Adres/NumerDomu"/>
                                            <xsl:if test="Adres/NumerLokalu != ''">/<xsl:value-of
                                                    select="Adres/NumerLokalu"/>
                                            </xsl:if><br /><xsl:value-of select="Adres/KodPocztowy"/>&#160;<xsl:value-of
                                                select="Adres/Miejscowosc"/><br />
                                        </xsl:for-each>
                                    </td>
                                    <td><xsl:value-of select="DataPrzyjeciaWychowankaDoPlacowki"/></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="left">
                                        <xsl:variable name="DataSkresleniaZListyWychowankow" select="DataSkresleniaZListyWychowankow"/>
                                        <xsl:if test="$DataSkresleniaZListyWychowankow != ''">
                                            <b>Data skreślenia z listy wychowanków:</b><br />
                                            <xsl:value-of select="DataSkresleniaZListyWychowankow"/><br /><br />
                                        </xsl:if>
                                        <xsl:variable name="PrzyczynaSkresleniaZListyWychowankow" select="PrzyczynaSkresleniaZListyWychowankow"/>
                                        <xsl:if test="$PrzyczynaSkresleniaZListyWychowankow != ''">
                                            <b>Przyczyna skreślenai z listy wychowanków:</b><br />
                                            <xsl:value-of select="PrzyczynaSkresleniaZListyWychowankow"/><br /><br />
                                        </xsl:if>
                                        <xsl:variable name="NazwaPlacowkiDoKtorejWychowanekZostalPrzeniesiony" select="NazwaPlacowkiDoKtorejWychowanekZostalPrzeniesiony"/>
                                        <xsl:if test="$NazwaPlacowkiDoKtorejWychowanekZostalPrzeniesiony != ''">
                                            <b>Nazwa placówki, do której wychowanek został przeniesiony:</b><br />
                                            <xsl:value-of select="NazwaPlacowkiDoKtorejWychowanekZostalPrzeniesiony"/><br /><br />
                                        </xsl:if>
                                        <xsl:variable name="AdresDoKtorejWychowanekZostalPrzeniesiony" select="AdresDoKtorejWychowanekZostalPrzeniesiony"/>
                                        <xsl:if test="$AdresDoKtorejWychowanekZostalPrzeniesiony != ''">
                                            <b>Adres placówki, do której wychowanek został przeniesiony:</b><br />
                                            <xsl:if test="AdresDoKtorejWychowanekZostalPrzeniesiony/Ulica != ''">ul.&#160;<xsl:value-of select="AdresDoKtorejWychowanekZostalPrzeniesiony/Ulica"/></xsl:if>&#160;<xsl:value-of select="AdresDoKtorejWychowanekZostalPrzeniesiony/NumerDomu"/><xsl:if test="AdresDoKtorejWychowanekZostalPrzeniesiony/NumerLokalu != ''">/<xsl:value-of select="AdresDoKtorejWychowanekZostalPrzeniesiony/NumerLokalu"/></xsl:if><br /><xsl:value-of select="AdresDoKtorejWychowanekZostalPrzeniesiony/KodPocztowy"/>&#160;<xsl:value-of select="AdresDoKtorejWychowanekZostalPrzeniesiony/Miejscowosc"/>
                                        </xsl:if>
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
