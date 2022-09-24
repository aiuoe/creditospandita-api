<?php

use Illuminate\Database\Seeder;
use App\Repositories\CountryRepositoryEloquent;

class CountriesSeeder extends Seeder
{

    /**
     * @var CountryRepositoryEloquent
     */
    protected $repository;

    /**
     * @var $countries
     */
    protected $countries = [
        'MEDELLIN-ANTIOQUIA',
        'BOGOTA',
        'D.C.-BOGOTA',
        'CALI-VALLEDELCAUCA',
        'PEREIRA-RISARALDA',
        'IBAGUE-TOLIMA',
        'BARRANQUILLA-ATLANTICO',
        'LETICIA-AMAZONAS',
        'ARAUCA-ARAUCA',
        'CARTAGENA-BOLIVAR',
        'TUNJA-BOYACA',
        'MANIZALES-CALDAS',
        'FLORENCIA-CAQUETA',
        'YOPAL-CASANARE',
        'POPAYAN-CAUCA',
        'VALLEDUPAR-CESAR',
        'QUIBDO-CHOCO',
        'MONTERIA-CORDOBA',
        'INIRIDA-GUAINIA',
        'SANJOSEDELGUAVIARE-GUAVIARE',
        'NEIVA-HUILA',
        'RIOHACHA-LAGUAJIRA',
        'SANTAMARTA-MAGDALENA',
        'VILLAVICENCIO-META',
        'PASTO-NARIÑO',
        'CUCUTA-N.DESANTANDER',
        'MOCOA-PUTUMAYO',
        'ARMENIA-QUINDIO',
        'SANANDRES-SANANDRES',
        'BUCARAMANGA-SANTANDER',
        'SINCELEJO-SUCRE',
        'MITU-VAUPES',
        'PUERTOCARREÑO-VICHADA',
        'BARRANCABERMEJA-SANTANDER',
        'BELLO-ANTIOQUIA',
        'ANDALUCIA-VALLEDELCAUCA',
        'SEVILLA-VALLEDELCAUCA',
        'BUGALAGRANDE-VALLEDELCAUCA',
        'GINEBRA-VALLEDELCAUCA',
        'ZARZAL-VALLEDELCAUCA',
        'LAVICTORIA-VALLEDELCAUCA',
        'GUADALAJARADEBUGA-VALLEDELCAUCA',
        'OBANDO-VALLEDELCAUCA',
        'CARTAGO-VALLEDELCAUCA',
        'ROLDANILLO-VALLEDELCAUCA',
        'PALMIRA-VALLEDELCAUCA',
        'JAMUNDI-VALLEDELCAUCA',
        'LADORADA-CALDAS',
        'VICTORIA-CALDAS',
        'PENSILVANIA-CALDAS',
        'MARQUETALIA-CALDAS',
        'ACACIAS-META',
        'AGUACHICA-CESAR',
        'APARTADO-ANTIOQUIA',
        'ARJONA-BOLIVAR',
        'BARANOA-ATLANTICO',
        'BUENAVENTURA-VALLEDELCAUCA',
        'CALARCA-QUINDIO',
        'CALDAS-ANTIOQUIA',
        'CAUCASIA-ANTIOQUIA',
        'CERETE-CORDOBA',
        'CHIA-CUNDINAMARCA',
        'CHIGORODO-ANTIOQUIA',
        'CHINCHINA-CALDAS',
        'CHIQUINQUIRA-BOYACA',
        'CIENAGA-MAGDALENA',
        'COPACABANA-ANTIOQUIA',
        'COROZAL-SUCRE',
        'DOSQUEBRADAS-RISARALDA',
        'DUITAMA-BOYACA',
        'ELCARMENDEBOLIVAR-BOLIVAR',
        'ENVIGADO-ANTIOQUIA',
        'ESPINAL-TOLIMA',
        'FACATATIVA-CUNDINAMARCA',
        'FLORIDA-VALLEDELCAUCA',
        'FLORIDABLANCA-SANTANDER',
        'FUNDACION-MAGDALENA',
        'FUNZA-CUNDINAMARCA',
        'FUSAGASUGA-CUNDINAMARCA',
        'GIRARDOT-CUNDINAMARCA',
        'GIRON-SANTANDER',
        'GRANADA-ANTIOQUIA',
        'GRANADA-CUNDINAMARCA',
        'GRANADA-META',
        'IPIALES-NARIÑO',
        'ITAGUI-ANTIOQUIA',
        'LACELIA-RISARALDA',
        'LORICA-CORDOBA',
        'LOSPATIOS-N.DESANTANDER',
        'MADRID-CUNDINAMARCA',
        'MAGANGUE-BOLIVAR',
        'MAICAO-LAGUAJIRA',
        'MALAMBO-ATLANTICO',
        'MONTELIBANO-CORDOBA',
        'MOSQUERA-CUNDINAMARCA',
        'OCAÑA-N.DESANTANDER',
        'PAMPLONA-N.DESANTANDER',
        'PIEDECUESTA-SANTANDER',
        'PITALITO-HUILA',
        'PLANETARICA-CORDOBA',
        'PLATO-MAGDALENA',
        'PRADERA-VALLEDELCAUCA',
        'PUERTOTEJADA-CAUCA',
        'RIONEGRO-ANTIOQUIA',
        'SABANALARGA-ATLANTICO',
        'SAHAGUN-CORDOBA',
        'SANTAROSADECABAL-RISARALDA',
        'SANTANDERDEQUILICHAO-CAUCA',
        'SOACHA-CUNDINAMARCA',
        'SOGAMOSO-BOYACA',
        'SOLEDAD-ATLANTICO',
        'TULUA-VALLEDELCAUCA',
        'TURBACO-BOLIVAR',
        'TURBO-ANTIOQUIA',
        'VILLADELROSARIO-N.DESANTANDER',
        'VILLAMARIA-CALDAS',
        'YUMBO-VALLEDELCAUCA',
        'ZIPAQUIRA-CUNDINAMARCA',
        'ABEJORRAL-ANTIOQUIA',
        'ABRIAQUI-ANTIOQUIA',
        'ALEJANDRIA-ANTIOQUIA',
        'AMAGA-ANTIOQUIA',
        'AMALFI-ANTIOQUIA',
        'ANDES-ANTIOQUIA',
        'ANGELOPOLIS-ANTIOQUIA',
        'ANGOSTURA-ANTIOQUIA',
        'ANORI-ANTIOQUIA',
        'SANTAFEDEANTIOQUIA-ANTIOQUIA',
        'ANZA-ANTIOQUIA',
        'ARBOLETES-ANTIOQUIA',
        'ARGELIA-ANTIOQUIA',
        'ARMENIA-ANTIOQUIA',
        'BARBOSA-ANTIOQUIA',
        'BELMIRA-ANTIOQUIA',
        'BETANIA-ANTIOQUIA',
        'BETULIA-ANTIOQUIA',
        'CIUDADBOLIVAR-ANTIOQUIA',
        'BRICEÑO-ANTIOQUIA',
        'BURITICA-ANTIOQUIA',
        'CACERES-ANTIOQUIA',
        'CAICEDO-ANTIOQUIA',
        'CAMPAMENTO-ANTIOQUIA',
        'CAÑASGORDAS-ANTIOQUIA',
        'CARACOLI-ANTIOQUIA',
        'CARAMANTA-ANTIOQUIA',
        'CAREPA-ANTIOQUIA',
        'ELCARMENDEVIBORAL-ANTIOQUIA',
        'CAROLINA-ANTIOQUIA',
        'CISNEROS-ANTIOQUIA',
        'COCORNA-ANTIOQUIA',
        'CONCEPCION-ANTIOQUIA',
        'CONCORDIA-ANTIOQUIA',
        'DABEIBA-ANTIOQUIA',
        'DONMATIAS-ANTIOQUIA',
        'EBEJICO-ANTIOQUIA',
        'ELBAGRE-ANTIOQUIA',
        'ENTRERRIOS-ANTIOQUIA',
        'FREDONIA-ANTIOQUIA',
        'FRONTINO-ANTIOQUIA',
        'GIRALDO-ANTIOQUIA',
        'GIRARDOTA-ANTIOQUIA',
        'GOMEZPLATA-ANTIOQUIA',
        'GUADALUPE-ANTIOQUIA',
        'GUARNE-ANTIOQUIA',
        'GUATAPE-ANTIOQUIA',
        'HELICONIA-ANTIOQUIA',
        'HISPANIA-ANTIOQUIA',
        'ITUANGO-ANTIOQUIA',
        'JARDIN-ANTIOQUIA',
        'JERICO-ANTIOQUIA',
        'LACEJA-ANTIOQUIA',
        'LAESTRELLA-ANTIOQUIA',
        'LAPINTADA-ANTIOQUIA',
        'LAUNION-ANTIOQUIA',
        'LIBORINA-ANTIOQUIA',
        'MACEO-ANTIOQUIA',
        'MARINILLA-ANTIOQUIA',
        'MONTEBELLO-ANTIOQUIA',
        'MURINDO-ANTIOQUIA',
        'MUTATA-ANTIOQUIA',
        'NARIÑO-ANTIOQUIA',
        'NECOCLI-ANTIOQUIA',
        'NECHI-ANTIOQUIA',
        'OLAYA-ANTIOQUIA',
        'PEÐOL-ANTIOQUIA',
        'PEQUE-ANTIOQUIA',
        'PUEBLORRICO-ANTIOQUIA',
        'PUERTOBERRIO-ANTIOQUIA',
        'PUERTONARE-ANTIOQUIA',
        'PUERTOTRIUNFO-ANTIOQUIA',
        'REMEDIOS-ANTIOQUIA',
        'RETIRO-ANTIOQUIA',
        'SABANALARGA-ANTIOQUIA',
        'SABANETA-ANTIOQUIA',
        'SALGAR-ANTIOQUIA',
        'SANANDRESDECUERQUIA-ANTIOQUIA',
        'SANCARLOS-ANTIOQUIA',
        'SANFRANCISCO-ANTIOQUIA',
        'SANJERONIMO-ANTIOQUIA',
        'SANJOSEDELAMONTAÑA-ANTIOQUIA',
        'SANJUANDEURABA-ANTIOQUIA',
        'SANLUIS-ANTIOQUIA',
        'SANPEDRO-ANTIOQUIA',
        'SANPEDRODEURABA-ANTIOQUIA',
        'SANRAFAEL-ANTIOQUIA',
        'SANROQUE-ANTIOQUIA',
        'SANVICENTE-ANTIOQUIA',
        'SANTABARBARA-ANTIOQUIA',
        'SANTAROSADEOSOS-ANTIOQUIA',
        'SANTODOMINGO-ANTIOQUIA',
        'ELSANTUARIO-ANTIOQUIA',
        'SEGOVIA-ANTIOQUIA',
        'SONSON-ANTIOQUIA',
        'SOPETRAN-ANTIOQUIA',
        'TAMESIS-ANTIOQUIA',
        'TARAZA-ANTIOQUIA',
        'TARSO-ANTIOQUIA',
        'TITIRIBI-ANTIOQUIA',
        'TOLEDO-ANTIOQUIA',
        'URAMITA-ANTIOQUIA',
        'URRAO-ANTIOQUIA',
        'VALDIVIA-ANTIOQUIA',
        'VALPARAISO-ANTIOQUIA',
        'VEGACHI-ANTIOQUIA',
        'VENECIA-ANTIOQUIA',
        'VIGIADELFUERTE-ANTIOQUIA',
        'YALI-ANTIOQUIA',
        'YARUMAL-ANTIOQUIA',
        'YOLOMBO-ANTIOQUIA',
        'YONDO-ANTIOQUIA',
        'ZARAGOZA-ANTIOQUIA',
        'CAMPODELACRUZ-ATLANTICO',
        'CANDELARIA-ATLANTICO',
        'GALAPA-ATLANTICO',
        'JUANDEACOSTA-ATLANTICO',
        'LURUACO-ATLANTICO',
        'MANATI-ATLANTICO',
        'PALMARDEVARELA-ATLANTICO',
        'PIOJO-ATLANTICO',
        'POLONUEVO-ATLANTICO',
        'PONEDERA-ATLANTICO',
        'PUERTOCOLOMBIA-ATLANTICO',
        'REPELON-ATLANTICO',
        'SABANAGRANDE-ATLANTICO',
        'SANTALUCIA-ATLANTICO',
        'SANTOTOMAS-ATLANTICO',
        'SUAN-ATLANTICO',
        'TUBARA-ATLANTICO',
        'USIACURI-ATLANTICO',
        'ACHI-BOLIVAR',
        'ALTOSDELROSARIO-BOLIVAR',
        'ARENAL-BOLIVAR',
        'ARROYOHONDO-BOLIVAR',
        'BARRANCODELOBA-BOLIVAR',
        'CALAMAR-BOLIVAR',
        'CANTAGALLO-BOLIVAR',
        'CICUCO-BOLIVAR',
        'CORDOBA-BOLIVAR',
        'CLEMENCIA-BOLIVAR',
        'ELGUAMO-BOLIVAR',
        'ELPEÑON-BOLIVAR',
        'HATILLODELOBA-BOLIVAR',
        'MAHATES-BOLIVAR',
        'MARGARITA-BOLIVAR',
        'MARIALABAJA-BOLIVAR',
        'MONTECRISTO-BOLIVAR',
        'MOMPOS-BOLIVAR',
        'NOROSI-BOLIVAR',
        'MORALES-BOLIVAR',
        'PINILLOS-BOLIVAR',
        'REGIDOR-BOLIVAR',
        'RIOVIEJO-BOLIVAR',
        'SANCRISTOBAL-BOLIVAR',
        'SANESTANISLAO-BOLIVAR',
        'SANFERNANDO-BOLIVAR',
        'SANJACINTO-BOLIVAR',
        'SANJACINTODELCAUCA-BOLIVAR',
        'SANJUANNEPOMUCENO-BOLIVAR',
        'SANMARTINDELOBA-BOLIVAR',
        'SANPABLO-BOLIVAR',
        'SANTACATALINA-BOLIVAR',
        'SANTAROSA-BOLIVAR',
        'SANTAROSADELSUR-BOLIVAR',
        'SIMITI-BOLIVAR',
        'SOPLAVIENTO-BOLIVAR',
        'TALAIGUANUEVO-BOLIVAR',
        'TIQUISIO-BOLIVAR',
        'TURBANA-BOLIVAR',
        'VILLANUEVA-BOLIVAR',
        'ZAMBRANO-BOLIVAR',
        'ALMEIDA-BOYACA',
        'AQUITANIA-BOYACA',
        'ARCABUCO-BOYACA',
        'BELEN-BOYACA',
        'BERBEO-BOYACA',
        'BETEITIVA-BOYACA',
        'BOAVITA-BOYACA',
        'BOYACA-BOYACA',
        'BRICEÑO-BOYACA',
        'BUENAVISTA-BOYACA',
        'BUSBANZA-BOYACA',
        'CALDAS-BOYACA',
        'CAMPOHERMOSO-BOYACA',
        'CERINZA-BOYACA',
        'CHINAVITA-BOYACA',
        'CHISCAS-BOYACA',
        'CHITA-BOYACA',
        'CHITARAQUE-BOYACA',
        'CHIVATA-BOYACA',
        'CIENEGA-BOYACA',
        'COMBITA-BOYACA',
        'COPER-BOYACA',
        'CORRALES-BOYACA',
        'COVARACHIA-BOYACA',
        'CUBARA-BOYACA',
        'CUCAITA-BOYACA',
        'CUITIVA-BOYACA',
        'CHIQUIZA-BOYACA',
        'CHIVOR-BOYACA',
        'ELCOCUY-BOYACA',
        'ELESPINO-BOYACA',
        'FIRAVITOBA-BOYACA',
        'FLORESTA-BOYACA',
        'GACHANTIVA-BOYACA',
        'GAMEZA-BOYACA',
        'GARAGOA-BOYACA',
        'GUACAMAYAS-BOYACA',
        'GUATEQUE-BOYACA',
        'GUAYATA-BOYACA',
        'GsICAN-BOYACA',
        'IZA-BOYACA',
        'JENESANO-BOYACA',
        'JERICO-BOYACA',
        'LABRANZAGRANDE-BOYACA',
        'LACAPILLA-BOYACA',
        'LAVICTORIA-BOYACA',
        'LAUVITA-BOYACA',
        'VILLADELEYVA-BOYACA',
        'MACANAL-BOYACA',
        'MARIPI-BOYACA',
        'MIRAFLORES-BOYACA',
        'MONGUA-BOYACA',
        'MONGUI-BOYACA',
        'MONIQUIRA-BOYACA',
        'MOTAVITA-BOYACA',
        'MUZO-BOYACA',
        'NOBSA-BOYACA',
        'NUEVOCOLON-BOYACA',
        'OICATA-BOYACA',
        'OTANCHE-BOYACA',
        'PACHAVITA-BOYACA',
        'PAEZ-BOYACA',
        'PAIPA-BOYACA',
        'PAJARITO-BOYACA',
        'PANQUEBA-BOYACA',
        'PAUNA-BOYACA',
        'PAYA-BOYACA',
        'PAZDERIO-BOYACA',
        'PESCA-BOYACA',
        'PISBA-BOYACA',
        'PUERTOBOYACA-BOYACA',
        'QUIPAMA-BOYACA',
        'RAMIRIQUI-BOYACA',
        'RAQUIRA-BOYACA',
        'RONDON-BOYACA',
        'SABOYA-BOYACA',
        'SACHICA-BOYACA',
        'SAMACA-BOYACA',
        'SANEDUARDO-BOYACA',
        'SANJOSEDEPARE-BOYACA',
        'SANLUISDEGACENO-BOYACA',
        'SANMATEO-BOYACA',
        'SANMIGUELDESEMA-BOYACA',
        'SANPABLODEBORBUR-BOYACA',
        'SANTANA-BOYACA',
        'SANTAMARIA-BOYACA',
        'SANTAROSADEVITERBO-BOYACA',
        'SANTASOFIA-BOYACA',
        'SATIVANORTE-BOYACA',
        'SATIVASUR-BOYACA',
        'SIACHOQUE-BOYACA',
        'SOATA-BOYACA',
        'SOCOTA-BOYACA',
        'SOCHA-BOYACA',
        'SOMONDOCO-BOYACA',
        'SORA-BOYACA',
        'SOTAQUIRA-BOYACA',
        'SORACA-BOYACA',
        'SUSACON-BOYACA',
        'SUTAMARCHAN-BOYACA',
        'SUTATENZA-BOYACA',
        'TASCO-BOYACA',
        'TENZA-BOYACA',
        'TIBANA-BOYACA',
        'TIBASOSA-BOYACA',
        'TINJACA-BOYACA',
        'TIPACOQUE-BOYACA',
        'TOCA-BOYACA',
        'TOGsI-BOYACA',
        'TOPAGA-BOYACA',
        'TOTA-BOYACA',
        'TUNUNGUA-BOYACA',
        'TURMEQUE-BOYACA',
        'TUTA-BOYACA',
        'TUTAZA-BOYACA',
        'UMBITA-BOYACA',
        'VENTAQUEMADA-BOYACA',
        'VIRACACHA-BOYACA',
        'ZETAQUIRA-BOYACA',
        'AGUADAS-CALDAS',
        'ANSERMA-CALDAS',
        'ARANZAZU-CALDAS',
        'BELALCAZAR-CALDAS',
        'FILADELFIA-CALDAS',
        'LAMERCED-CALDAS',
        'MANZANARES-CALDAS',
        'MARMATO-CALDAS',
        'MARULANDA-CALDAS',
        'NEIRA-CALDAS',
        'NORCASIA-CALDAS',
        'PACORA-CALDAS',
        'PALESTINA-CALDAS',
        'RIOSUCIO-CALDAS',
        'RISARALDA-CALDAS',
        'SALAMINA-CALDAS',
        'SAMANA-CALDAS',
        'SANJOSE-CALDAS',
        'SUPIA-CALDAS',
        'VITERBO-CALDAS',
        'ALBANIA-CAQUETA',
        'BELENDELOSANDAQUIES-CAQUETA',
        'CARTAGENADELCHAIRA-CAQUETA',
        'CURILLO-CAQUETA',
        'ELDONCELLO-CAQUETA',
        'ELPAUJIL-CAQUETA',
        'LAMONTAÑITA-CAQUETA',
        'MILAN-CAQUETA',
        'MORELIA-CAQUETA',
        'PUERTORICO-CAQUETA',
        'SANJOSEDELFRAGUA-CAQUETA',
        'SANVICENTEDELCAGUAN-CAQUETA',
        'SOLANO-CAQUETA',
        'SOLITA-CAQUETA',
        'VALPARAISO-CAQUETA',
        'ALMAGUER-CAUCA',
        'ARGELIA-CAUCA',
        'BALBOA-CAUCA',
        'BOLIVAR-CAUCA',
        'BUENOSAIRES-CAUCA',
        'CAJIBIO-CAUCA',
        'CALDONO-CAUCA',
        'CALOTO-CAUCA',
        'CORINTO-CAUCA',
        'ELTAMBO-CAUCA',
        'FLORENCIA-CAUCA',
        'GUACHENE-CAUCA',
        'GUAPI-CAUCA',
        'INZA-CAUCA',
        'JAMBALO-CAUCA',
        'LASIERRA-CAUCA',
        'LAVEGA-CAUCA',
        'LOPEZ-CAUCA',
        'MERCADERES-CAUCA',
        'MIRANDA-CAUCA',
        'MORALES-CAUCA',
        'PADILLA-CAUCA',
        'PAEZ-CAUCA',
        'PATIA-CAUCA',
        'PIAMONTE-CAUCA',
        'PIENDAMO-CAUCA',
        'PURACE-CAUCA',
        'ROSAS-CAUCA',
        'SANSEBASTIAN-CAUCA',
        'SANTAROSA-CAUCA',
        'SILVIA-CAUCA',
        'SOTARA-CAUCA',
        'SUAREZ-CAUCA',
        'SUCRE-CAUCA',
        'TIMBIO-CAUCA',
        'TIMBIQUI-CAUCA',
        'TORIBIO-CAUCA',
        'TOTORO-CAUCA',
        'VILLARICA-CAUCA',
        'AGUSTINCODAZZI-CESAR',
        'ASTREA-CESAR',
        'BECERRIL-CESAR',
        'BOSCONIA-CESAR',
        'CHIMICHAGUA-CESAR',
        'CHIRIGUANA-CESAR',
        'CURUMANI-CESAR',
        'ELCOPEY-CESAR',
        'ELPASO-CESAR',
        'GAMARRA-CESAR',
        'GONZALEZ-CESAR',
        'LAGLORIA-CESAR',
        'LAJAGUADEIBIRICO-CESAR',
        'MANAURE-CESAR',
        'PAILITAS-CESAR',
        'PELAYA-CESAR',
        'PUEBLOBELLO-CESAR',
        'RIODEORO-CESAR',
        'LAPAZ-CESAR',
        'SANALBERTO-CESAR',
        'SANDIEGO-CESAR',
        'SANMARTIN-CESAR',
        'TAMALAMEQUE-CESAR',
        'AYAPEL-CORDOBA',
        'BUENAVISTA-CORDOBA',
        'CANALETE-CORDOBA',
        'CHIMA-CORDOBA',
        'CHINU-CORDOBA',
        'CIENAGADEORO-CORDOBA',
        'COTORRA-CORDOBA',
        'LAAPARTADA-CORDOBA',
        'LOSCORDOBAS-CORDOBA',
        'MOMIL-CORDOBA',
        'MOÑITOS-CORDOBA',
        'PUEBLONUEVO-CORDOBA',
        'PUERTOESCONDIDO-CORDOBA',
        'PUERTOLIBERTADOR-CORDOBA',
        'PURISIMA-CORDOBA',
        'SANANDRESSOTAVENTO-CORDOBA',
        'SANANTERO-CORDOBA',
        'SANBERNARDODELVIENTO-CORDOBA',
        'SANCARLOS-CORDOBA',
        'SANPELAYO-CORDOBA',
        'TIERRALTA-CORDOBA',
        'VALENCIA-CORDOBA',
        'AGUADEDIOS-CUNDINAMARCA',
        'ALBAN-CUNDINAMARCA',
        'ANAPOIMA-CUNDINAMARCA',
        'ANOLAIMA-CUNDINAMARCA',
        'ARBELAEZ-CUNDINAMARCA',
        'BELTRAN-CUNDINAMARCA',
        'BITUIMA-CUNDINAMARCA',
        'BOJACA-CUNDINAMARCA',
        'CABRERA-CUNDINAMARCA',
        'CACHIPAY-CUNDINAMARCA',
        'CAJICA-CUNDINAMARCA',
        'CAPARRAPI-CUNDINAMARCA',
        'CAQUEZA-CUNDINAMARCA',
        'CARMENDECARUPA-CUNDINAMARCA',
        'CHAGUANI-CUNDINAMARCA',
        'CHIPAQUE-CUNDINAMARCA',
        'CHOACHI-CUNDINAMARCA',
        'CHOCONTA-CUNDINAMARCA',
        'COGUA-CUNDINAMARCA',
        'COTA-CUNDINAMARCA',
        'CUCUNUBA-CUNDINAMARCA',
        'ELCOLEGIO-CUNDINAMARCA',
        'ELPEÑON-CUNDINAMARCA',
        'ELROSAL-CUNDINAMARCA',
        'FOMEQUE-CUNDINAMARCA',
        'FOSCA-CUNDINAMARCA',
        'FUQUENE-CUNDINAMARCA',
        'GACHALA-CUNDINAMARCA',
        'GACHANCIPA-CUNDINAMARCA',
        'GACHETA-CUNDINAMARCA',
        'GAMA-CUNDINAMARCA',
        'GUACHETA-CUNDINAMARCA',
        'GUADUAS-CUNDINAMARCA',
        'GUASCA-CUNDINAMARCA',
        'GUATAQUI-CUNDINAMARCA',
        'GUATAVITA-CUNDINAMARCA',
        'GUAYABALDESIQUIMA-CUNDINAMARCA',
        'GUAYABETAL-CUNDINAMARCA',
        'GUTIERREZ-CUNDINAMARCA',
        'JERUSALEN-CUNDINAMARCA',
        'JUNIN-CUNDINAMARCA',
        'LACALERA-CUNDINAMARCA',
        'LAMESA-CUNDINAMARCA',
        'LAPALMA-CUNDINAMARCA',
        'LAPEÑA-CUNDINAMARCA',
        'LAVEGA-CUNDINAMARCA',
        'LENGUAZAQUE-CUNDINAMARCA',
        'MACHETA-CUNDINAMARCA',
        'MANTA-CUNDINAMARCA',
        'MEDINA-CUNDINAMARCA',
        'NARIÑO-CUNDINAMARCA',
        'NEMOCON-CUNDINAMARCA',
        'NILO-CUNDINAMARCA',
        'NIMAIMA-CUNDINAMARCA',
        'NOCAIMA-CUNDINAMARCA',
        'VENECIA-CUNDINAMARCA',
        'PACHO-CUNDINAMARCA',
        'PAIME-CUNDINAMARCA',
        'PANDI-CUNDINAMARCA',
        'PARATEBUENO-CUNDINAMARCA',
        'PASCA-CUNDINAMARCA',
        'PUERTOSALGAR-CUNDINAMARCA',
        'PULI-CUNDINAMARCA',
        'QUEBRADANEGRA-CUNDINAMARCA',
        'QUETAME-CUNDINAMARCA',
        'QUIPILE-CUNDINAMARCA',
        'APULO-CUNDINAMARCA',
        'RICAURTE-CUNDINAMARCA',
        'SANANTONIODELTEQUENDAMA-CUNDINAMARCA',
        'SANBERNARDO-CUNDINAMARCA',
        'SANCAYETANO-CUNDINAMARCA',
        'SANFRANCISCO-CUNDINAMARCA',
        'SANJUANDERIOSECO-CUNDINAMARCA',
        'SASAIMA-CUNDINAMARCA',
        'SESQUILE-CUNDINAMARCA',
        'SIBATE-CUNDINAMARCA',
        'SILVANIA-CUNDINAMARCA',
        'SIMIJACA-CUNDINAMARCA',
        'SOPO-CUNDINAMARCA',
        'SUBACHOQUE-CUNDINAMARCA',
        'SUESCA-CUNDINAMARCA',
        'SUPATA-CUNDINAMARCA',
        'SUSA-CUNDINAMARCA',
        'SUTATAUSA-CUNDINAMARCA',
        'TABIO-CUNDINAMARCA',
        'TAUSA-CUNDINAMARCA',
        'TENA-CUNDINAMARCA',
        'TENJO-CUNDINAMARCA',
        'TIBACUY-CUNDINAMARCA',
        'TIBIRITA-CUNDINAMARCA',
        'TOCAIMA-CUNDINAMARCA',
        'TOCANCIPA-CUNDINAMARCA',
        'TOPAIPI-CUNDINAMARCA',
        'UBALA-CUNDINAMARCA',
        'UBAQUE-CUNDINAMARCA',
        'VILLADESANDIEGODEUBATE-CUNDINAMARCA',
        'UNE-CUNDINAMARCA',
        'UTICA-CUNDINAMARCA',
        'VERGARA-CUNDINAMARCA',
        'VIANI-CUNDINAMARCA',
        'VILLAGOMEZ-CUNDINAMARCA',
        'VILLAPINZON-CUNDINAMARCA',
        'VILLETA-CUNDINAMARCA',
        'VIOTA-CUNDINAMARCA',
        'YACOPI-CUNDINAMARCA',
        'ZIPACON-CUNDINAMARCA',
        'ACANDI-CHOCO',
        'ALTOBAUDO-CHOCO',
        'ATRATO-CHOCO',
        'BAGADO-CHOCO',
        'BAHIASOLANO-CHOCO',
        'BAJOBAUDO-CHOCO',
        'BOJAYA-CHOCO',
        'ELCANTONDELSANPABLO-CHOCO',
        'CARMENDELDARIEN-CHOCO',
        'CERTEGUI-CHOCO',
        'CONDOTO-CHOCO',
        'ELCARMENDEATRATO-CHOCO',
        'ELLITORALDELSANJUAN-CHOCO',
        'ISTMINA-CHOCO',
        'JURADO-CHOCO',
        'LLORO-CHOCO',
        'MEDIOATRATO-CHOCO',
        'MEDIOBAUDO-CHOCO',
        'MEDIOSANJUAN-CHOCO',
        'NOVITA-CHOCO',
        'NUQUI-CHOCO',
        'RIOIRO-CHOCO',
        'RIOQUITO-CHOCO',
        'RIOSUCIO-CHOCO',
        'SANJOSEDELPALMAR-CHOCO',
        'SIPI-CHOCO',
        'TADO-CHOCO',
        'UNGUIA-CHOCO',
        'UNIONPANAMERICANA-CHOCO',
        'ACEVEDO-HUILA',
        'AGRADO-HUILA',
        'AIPE-HUILA',
        'ALGECIRAS-HUILA',
        'ALTAMIRA-HUILA',
        'BARAYA-HUILA',
        'CAMPOALEGRE-HUILA',
        'COLOMBIA-HUILA',
        'ELIAS-HUILA',
        'GARZON-HUILA',
        'GIGANTE-HUILA',
        'GUADALUPE-HUILA',
        'HOBO-HUILA',
        'IQUIRA-HUILA',
        'ISNOS-HUILA',
        'LAARGENTINA-HUILA',
        'LAPLATA-HUILA',
        'NATAGA-HUILA',
        'OPORAPA-HUILA',
        'PAICOL-HUILA',
        'PALERMO-HUILA',
        'PALESTINA-HUILA',
        'PITAL-HUILA',
        'RIVERA-HUILA',
        'SALADOBLANCO-HUILA',
        'SANAGUSTIN-HUILA',
        'SANTAMARIA-HUILA',
        'SUAZA-HUILA',
        'TARQUI-HUILA',
        'TESALIA-HUILA',
        'TELLO-HUILA',
        'TERUEL-HUILA',
        'TIMANA-HUILA',
        'VILLAVIEJA-HUILA',
        'YAGUARA-HUILA',
        'ALBANIA-LAGUAJIRA',
        'BARRANCAS-LAGUAJIRA',
        'DIBULLA-LAGUAJIRA',
        'DISTRACCION-LAGUAJIRA',
        'ELMOLINO-LAGUAJIRA',
        'FONSECA-LAGUAJIRA',
        'HATONUEVO-LAGUAJIRA',
        'LAJAGUADELPILAR-LAGUAJIRA',
        'MANAURE-LAGUAJIRA',
        'SANJUANDELCESAR-LAGUAJIRA',
        'URIBIA-LAGUAJIRA',
        'URUMITA-LAGUAJIRA',
        'VILLANUEVA-LAGUAJIRA',
        'ALGARROBO-MAGDALENA',
        'ARACATACA-MAGDALENA',
        'ARIGUANI-MAGDALENA',
        'CERROSANANTONIO-MAGDALENA',
        'CHIBOLO-MAGDALENA',
        'CONCORDIA-MAGDALENA',
        'ELBANCO-MAGDALENA',
        'ELPIÑON-MAGDALENA',
        'ELRETEN-MAGDALENA',
        'GUAMAL-MAGDALENA',
        'NUEVAGRANADA-MAGDALENA',
        'PEDRAZA-MAGDALENA',
        'PIJIÑODELCARMEN-MAGDALENA',
        'PIVIJAY-MAGDALENA',
        'PUEBLOVIEJO-MAGDALENA',
        'REMOLINO-MAGDALENA',
        'SABANASDESANANGEL-MAGDALENA',
        'SALAMINA-MAGDALENA',
        'SANSEBASTIANDEBUENAVISTA-MAGDALENA',
        'SANZENON-MAGDALENA',
        'SANTAANA-MAGDALENA',
        'SANTABARBARADEPINTO-MAGDALENA',
        'SITIONUEVO-MAGDALENA',
        'TENERIFE-MAGDALENA',
        'ZAPAYAN-MAGDALENA',
        'ZONABANANERA-MAGDALENA',
        'BARRANCADEUPIA-META',
        'CABUYARO-META',
        'CASTILLALANUEVA-META',
        'CUBARRAL-META',
        'CUMARAL-META',
        'ELCALVARIO-META',
        'ELCASTILLO-META',
        'ELDORADO-META',
        'FUENTEDEORO-META',
        'GUAMAL-META',
        'MAPIRIPAN-META',
        'MESETAS-META',
        'LAMACARENA-META',
        'URIBE-META',
        'LEJANIAS-META',
        'PUERTOCONCORDIA-META',
        'PUERTOGAITAN-META',
        'PUERTOLOPEZ-META',
        'PUERTOLLERAS-META',
        'PUERTORICO-META',
        'RESTREPO-META',
        'SANCARLOSDEGUAROA-META',
        'SANJUANDEARAMA-META',
        'SANJUANITO-META',
        'SANMARTIN-META',
        'VISTAHERMOSA-META',
        'ALBAN-NARIÑO',
        'ALDANA-NARIÑO',
        'ANCUYA-NARIÑO',
        'ARBOLEDA-NARIÑO',
        'BARBACOAS-NARIÑO',
        'BELEN-NARIÑO',
        'BUESACO-NARIÑO',
        'COLON-NARIÑO',
        'CONSACA-NARIÑO',
        'CONTADERO-NARIÑO',
        'CORDOBA-NARIÑO',
        'CUASPUD-NARIÑO',
        'CUMBAL-NARIÑO',
        'CUMBITARA-NARIÑO',
        'CHACHAGsI-NARIÑO',
        'ELCHARCO-NARIÑO',
        'ELPEÑOL-NARIÑO',
        'ELROSARIO-NARIÑO',
        'ELTABLONDEGOMEZ-NARIÑO',
        'ELTAMBO-NARIÑO',
        'FUNES-NARIÑO',
        'GUACHUCAL-NARIÑO',
        'GUAITARILLA-NARIÑO',
        'GUALMATAN-NARIÑO',
        'ILES-NARIÑO',
        'IMUES-NARIÑO',
        'LACRUZ-NARIÑO',
        'LAFLORIDA-NARIÑO',
        'LALLANADA-NARIÑO',
        'LATOLA-NARIÑO',
        'LAUNION-NARIÑO',
        'LEIVA-NARIÑO',
        'LINARES-NARIÑO',
        'LOSANDES-NARIÑO',
        'MAGsI-NARIÑO',
        'MALLAMA-NARIÑO',
        'MOSQUERA-NARIÑO',
        'NARIÑO-NARIÑO',
        'OLAYAHERRERA-NARIÑO',
        'OSPINA-NARIÑO',
        'FRANCISCOPIZARRO-NARIÑO',
        'POLICARPA-NARIÑO',
        'POTOSI-NARIÑO',
        'PROVIDENCIA-NARIÑO',
        'PUERRES-NARIÑO',
        'PUPIALES-NARIÑO',
        'RICAURTE-NARIÑO',
        'ROBERTOPAYAN-NARIÑO',
        'SAMANIEGO-NARIÑO',
        'SANDONA-NARIÑO',
        'SANBERNARDO-NARIÑO',
        'SANLORENZO-NARIÑO',
        'SANPABLO-NARIÑO',
        'SANPEDRODECARTAGO-NARIÑO',
        'SANTABARBARA-NARIÑO',
        'SANTACRUZ-NARIÑO',
        'SAPUYES-NARIÑO',
        'TAMINANGO-NARIÑO',
        'TANGUA-NARIÑO',
        'SANANDRESDETUMACO-NARIÑO',
        'TUQUERRES-NARIÑO',
        'YACUANQUER-NARIÑO',
        'ABREGO-N.DESANTANDER',
        'ARBOLEDAS-N.DESANTANDER',
        'BOCHALEMA-N.DESANTANDER',
        'BUCARASICA-N.DESANTANDER',
        'CACOTA-N.DESANTANDER',
        'CACHIRA-N.DESANTANDER',
        'CHINACOTA-N.DESANTANDER',
        'CHITAGA-N.DESANTANDER',
        'CONVENCION-N.DESANTANDER',
        'CUCUTILLA-N.DESANTANDER',
        'DURANIA-N.DESANTANDER',
        'ELCARMEN-N.DESANTANDER',
        'ELTARRA-N.DESANTANDER',
        'ELZULIA-N.DESANTANDER',
        'GRAMALOTE-N.DESANTANDER',
        'HACARI-N.DESANTANDER',
        'HERRAN-N.DESANTANDER',
        'LABATECA-N.DESANTANDER',
        'LAESPERANZA-N.DESANTANDER',
        'LAPLAYA-N.DESANTANDER',
        'LOURDES-N.DESANTANDER',
        'MUTISCUA-N.DESANTANDER',
        'PAMPLONITA-N.DESANTANDER',
        'PUERTOSANTANDER-N.DESANTANDER',
        'RAGONVALIA-N.DESANTANDER',
        'SALAZAR-N.DESANTANDER',
        'SANCALIXTO-N.DESANTANDER',
        'SANCAYETANO-N.DESANTANDER',
        'SANTIAGO-N.DESANTANDER',
        'SARDINATA-N.DESANTANDER',
        'SILOS-N.DESANTANDER',
        'TEORAMA-N.DESANTANDER',
        'TIBU-N.DESANTANDER',
        'TOLEDO-N.DESANTANDER',
        'VILLACARO-N.DESANTANDER',
        'BUENAVISTA-QUINDIO',
        'CIRCASIA-QUINDIO',
        'CORDOBA-QUINDIO',
        'FILANDIA-QUINDIO',
        'GENOVA-QUINDIO',
        'LATEBAIDA-QUINDIO',
        'MONTENEGRO-QUINDIO',
        'PIJAO-QUINDIO',
        'QUIMBAYA-QUINDIO',
        'SALENTO-QUINDIO',
        'APIA-RISARALDA',
        'BALBOA-RISARALDA',
        'BELENDEUMBRIA-RISARALDA',
        'GUATICA-RISARALDA',
        'LAVIRGINIA-RISARALDA',
        'MARSELLA-RISARALDA',
        'MISTRATO-RISARALDA',
        'PUEBLORICO-RISARALDA',
        'QUINCHIA-RISARALDA',
        'SANTUARIO-RISARALDA',
        'AGUADA-SANTANDER',
        'ALBANIA-SANTANDER',
        'ARATOCA-SANTANDER',
        'BARBOSA-SANTANDER',
        'BARICHARA-SANTANDER',
        'BETULIA-SANTANDER',
        'BOLIVAR-SANTANDER',
        'CABRERA-SANTANDER',
        'CALIFORNIA-SANTANDER',
        'CAPITANEJO-SANTANDER',
        'CARCASI-SANTANDER',
        'CEPITA-SANTANDER',
        'CERRITO-SANTANDER',
        'CHARALA-SANTANDER',
        'CHARTA-SANTANDER',
        'CHIMA-SANTANDER',
        'CHIPATA-SANTANDER',
        'CIMITARRA-SANTANDER',
        'CONCEPCION-SANTANDER',
        'CONFINES-SANTANDER',
        'CONTRATACION-SANTANDER',
        'COROMORO-SANTANDER',
        'CURITI-SANTANDER',
        'ELCARMENDECHUCURI-SANTANDER',
        'ELGUACAMAYO-SANTANDER',
        'ELPEÑON-SANTANDER',
        'ELPLAYON-SANTANDER',
        'ENCINO-SANTANDER',
        'ENCISO-SANTANDER',
        'FLORIAN-SANTANDER',
        'GALAN-SANTANDER',
        'GAMBITA-SANTANDER',
        'GUACA-SANTANDER',
        'GUADALUPE-SANTANDER',
        'GUAPOTA-SANTANDER',
        'GUAVATA-SANTANDER',
        'GsEPSA-SANTANDER',
        'HATO-SANTANDER',
        'JESUSMARIA-SANTANDER',
        'JORDAN-SANTANDER',
        'LABELLEZA-SANTANDER',
        'LANDAZURI-SANTANDER',
        'LAPAZ-SANTANDER',
        'LEBRIJA-SANTANDER',
        'LOSSANTOS-SANTANDER',
        'MACARAVITA-SANTANDER',
        'MALAGA-SANTANDER',
        'MATANZA-SANTANDER',
        'MOGOTES-SANTANDER',
        'MOLAGAVITA-SANTANDER',
        'OCAMONTE-SANTANDER',
        'OIBA-SANTANDER',
        'ONZAGA-SANTANDER',
        'PALMAR-SANTANDER',
        'PALMASDELSOCORRO-SANTANDER',
        'PARAMO-SANTANDER',
        'PINCHOTE-SANTANDER',
        'PUENTENACIONAL-SANTANDER',
        'PUERTOPARRA-SANTANDER',
        'PUERTOWILCHES-SANTANDER',
        'RIONEGRO-SANTANDER',
        'SABANADETORRES-SANTANDER',
        'SANANDRES-SANTANDER',
        'SANBENITO-SANTANDER',
        'SANGIL-SANTANDER',
        'SANJOAQUIN-SANTANDER',
        'SANJOSEDEMIRANDA-SANTANDER',
        'SANMIGUEL-SANTANDER',
        'SANVICENTEDECHUCURI-SANTANDER',
        'SANTABARBARA-SANTANDER',
        'SANTAHELENADELOPON-SANTANDER',
        'SIMACOTA-SANTANDER',
        'SOCORRO-SANTANDER',
        'SUAITA-SANTANDER',
        'SUCRE-SANTANDER',
        'SURATA-SANTANDER',
        'TONA-SANTANDER',
        'VALLEDESANJOSE-SANTANDER',
        'VELEZ-SANTANDER',
        'VETAS-SANTANDER',
        'VILLANUEVA-SANTANDER',
        'ZAPATOCA-SANTANDER',
        'BUENAVISTA-SUCRE',
        'CAIMITO-SUCRE',
        'COLOSO-SUCRE',
        'COVEÑAS-SUCRE',
        'CHALAN-SUCRE',
        'ELROBLE-SUCRE',
        'GALERAS-SUCRE',
        'GUARANDA-SUCRE',
        'LAUNION-SUCRE',
        'LOSPALMITOS-SUCRE',
        'MAJAGUAL-SUCRE',
        'MORROA-SUCRE',
        'OVEJAS-SUCRE',
        'PALMITO-SUCRE',
        'SAMPUES-SUCRE',
        'SANBENITOABAD-SUCRE',
        'SANJUANDEBETULIA-SUCRE',
        'SANMARCOS-SUCRE',
        'SANONOFRE-SUCRE',
        'SANPEDRO-SUCRE',
        'SANLUISDESINCE-SUCRE',
        'SUCRE-SUCRE',
        'SANTIAGODETOLU-SUCRE',
        'TOLUVIEJO-SUCRE',
        'ALPUJARRA-TOLIMA',
        'ALVARADO-TOLIMA',
        'AMBALEMA-TOLIMA',
        'ANZOATEGUI-TOLIMA',
        'ARMERO-TOLIMA',
        'ATACO-TOLIMA',
        'CAJAMARCA-TOLIMA',
        'CARMENDEAPICALA-TOLIMA',
        'CASABIANCA-TOLIMA',
        'CHAPARRAL-TOLIMA',
        'COELLO-TOLIMA',
        'COYAIMA-TOLIMA',
        'CUNDAY-TOLIMA',
        'DOLORES-TOLIMA',
        'FALAN-TOLIMA',
        'FLANDES-TOLIMA',
        'FRESNO-TOLIMA',
        'GUAMO-TOLIMA',
        'HERVEO-TOLIMA',
        'HONDA-TOLIMA',
        'ICONONZO-TOLIMA',
        'LERIDA-TOLIMA',
        'LIBANO-TOLIMA',
        'MARIQUITA-TOLIMA',
        'MELGAR-TOLIMA',
        'MURILLO-TOLIMA',
        'NATAGAIMA-TOLIMA',
        'ORTEGA-TOLIMA',
        'PALOCABILDO-TOLIMA',
        'PIEDRAS-TOLIMA',
        'PLANADAS-TOLIMA',
        'PRADO-TOLIMA',
        'PURIFICACION-TOLIMA',
        'RIOBLANCO-TOLIMA',
        'RONCESVALLES-TOLIMA',
        'ROVIRA-TOLIMA',
        'SALDAÑA-TOLIMA',
        'SANANTONIO-TOLIMA',
        'SANLUIS-TOLIMA',
        'SANTAISABEL-TOLIMA',
        'SUAREZ-TOLIMA',
        'VALLEDESANJUAN-TOLIMA',
        'VENADILLO-TOLIMA',
        'VILLAHERMOSA-TOLIMA',
        'VILLARRICA-TOLIMA',
        'ALCALA-VALLEDELCAUCA',
        'ANSERMANUEVO-VALLEDELCAUCA',
        'ARGELIA-VALLEDELCAUCA',
        'BOLIVAR-VALLEDELCAUCA',
        'CAICEDONIA-VALLEDELCAUCA',
        'CALIMA-VALLEDELCAUCA',
        'CANDELARIA-VALLEDELCAUCA',
        'DAGUA-VALLEDELCAUCA',
        'ELAGUILA-VALLEDELCAUCA',
        'ELCAIRO-VALLEDELCAUCA',
        'ELCERRITO-VALLEDELCAUCA',
        'ELDOVIO-VALLEDELCAUCA',
        'GUACARI-VALLEDELCAUCA',
        'LACUMBRE-VALLEDELCAUCA',
        'LAUNION-VALLEDELCAUCA',
        'RESTREPO-VALLEDELCAUCA',
        'RIOFRIO-VALLEDELCAUCA',
        'SANPEDRO-VALLEDELCAUCA',
        'TORO-VALLEDELCAUCA',
        'TRUJILLO-VALLEDELCAUCA',
        'ULLOA-VALLEDELCAUCA',
        'VERSALLES-VALLEDELCAUCA',
        'VIJES-VALLEDELCAUCA',
        'YOTOCO-VALLEDELCAUCA',
        'ARAUQUITA-ARAUCA',
        'CRAVONORTE-ARAUCA',
        'FORTUL-ARAUCA',
        'PUERTORONDON-ARAUCA',
        'SARAVENA-ARAUCA',
        'TAME-ARAUCA',
        'AGUAZUL-CASANARE',
        'CHAMEZA-CASANARE',
        'HATOCOROZAL-CASANARE',
        'LASALINA-CASANARE',
        'MANI-CASANARE',
        'MONTERREY-CASANARE',
        'NUNCHIA-CASANARE',
        'OROCUE-CASANARE',
        'PAZDEARIPORO-CASANARE',
        'PORE-CASANARE',
        'RECETOR-CASANARE',
        'SABANALARGA-CASANARE',
        'SACAMA-CASANARE',
        'SANLUISDEPALENQUE-CASANARE',
        'TAMARA-CASANARE',
        'TAURAMENA-CASANARE',
        'TRINIDAD-CASANARE',
        'VILLANUEVA-CASANARE',
        'COLON-PUTUMAYO',
        'ORITO-PUTUMAYO',
        'PUERTOASIS-PUTUMAYO',
        'PUERTOCAICEDO-PUTUMAYO',
        'PUERTOGUZMAN-PUTUMAYO',
        'LEGUIZAMO-PUTUMAYO',
        'SIBUNDOY-PUTUMAYO',
        'SANFRANCISCO-PUTUMAYO',
        'SANMIGUEL-PUTUMAYO',
        'SANTIAGO-PUTUMAYO',
        'VALLEDELGUAMUEZ-PUTUMAYO',
        'VILLAGARZON-PUTUMAYO',
        'PROVIDENCIA-SANANDRES',
        'ELENCANTO-AMAZONAS',
        'LACHORRERA-AMAZONAS',
        'LAPEDRERA-AMAZONAS',
        'LAVICTORIA-AMAZONAS',
        'MIRITI-PARANA-AMAZONAS',
        'PUERTOALEGRIA-AMAZONAS',
        'PUERTOARICA-AMAZONAS',
        'PUERTONARIÑO-AMAZONAS',
        'PUERTOSANTANDER-AMAZONAS',
        'TARAPACA-AMAZONAS',
        'BARRANCOMINAS-GUAINIA',
        'MAPIRIPANA-GUAINIA',
        'SANFELIPE-GUAINIA',
        'PUERTOCOLOMBIA-GUAINIA',
        'LAGUADALUPE-GUAINIA',
        'CACAHUAL-GUAINIA',
        'PANAPANA-GUAINIA',
        'MORICHAL-GUAINIA',
        'CALAMAR-GUAVIARE',
        'ELRETORNO-GUAVIARE',
        'MIRAFLORES-GUAVIARE',
        'CARURU-VAUPES',
        'PACOA-VAUPES',
        'TARAIRA-VAUPES',
        'PAPUNAUA-VAUPES',
        'YAVARATE-VAUPES',
        'LAPRIMAVERA-VICHADA',
        'SANTAROSALIA-VICHADA',
        'CUMARIBO-VICHADA',
        
    ];

    public function __construct(CountryRepositoryEloquent $repository){
        $this->repository = $repository;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach( $this->countries as $name )
        {
            $this->repository->create( ['name'=>$name] );
        }
    }
}