<?php

namespace dynamicart;

/**
 * User Agent Parser > UAParser
 * Parse any user agent string
 * Detect the device (mobile|tablet|smartTV|desktop or service)
 *  The device contain the brand (like Samsung), and contain the phone or tablet type (like Prestigio PAP5400DUO).
 * Detect visitor Operation System like Windows, Linux, OSX, Android...
 *  The OS info contain the OS version (like Windows NT5.1 or Android 4.2.2).
 * Detect visitor Browser/Client like Chrome, Firefox, Outlook...
 *  The browser info contain tha browser or client version (like MSOffice 15 or Chrome 38...)
 *
 * The mobile/tablet/os/browser patterns based on Mobile Detect Library 2.8.17, but this was just the start point.
 *
 * The patterns are continuously grow and change.
 *
 * @author János Szentgyörgyi <puttocska@gmail.com>
 *
 * @license https://raw.githubusercontent.com/dynamicart/UAParser/master/LICENSE MIT
 * @link http://mobiledetect.net Homepage
 * @link https://github.com/serbanghita/Mobile-Detect GitHub Repository
 *
 * @version 0.2.0
 */
class UAParser{
    const deviceMobile = 'mobile';
    const deviceTablet = 'tablet';
    const deviceService = 'service';
    const deviceDesktop = 'desktop';
    const deviceSmartTV = 'smartTV';

    /**
     * No desktop, mobile or tablet device, this server services..
     * @var array
     */
    private $patternService = array(
        'GoogleImageProxy'          => 'GoogleImageProxy',
        'mail-proxy'                => 'mail-proxy',
        'dalvik'                    => 'dalvik',
        'SpamBayes'                 => 'SpamBayes',
        'thebat.exedwnloadmnger'    => 'thebat.exedwnloadmnger',
    );

    /**
     * pattern for detecting SmartTV
     * https://udger.com/resources/ua-list/device-detail?device=Smart%20TV
     * @var array
     */
    private $patternSmartTV = array(
        'Philips'               => 'PhilipsTV',
        'LG'                    => 'NetCast',
        'unknown'               => 'smartTV',
    );

    /**
     * pattern for detecting mobile device
     * @var array
     */
    private $patternMobile = array(
        // Micromax must upper because the codes might conflict with Acer tags
        'Micromax'      => 'Micromax.*\b(A210|A92|A88|A72|A111|A110Q|A115|A116|A110|A90S|A26|A51|A35|A54|A25|A27|A89|A68|A65|A57|A90)\b',
        // Motorola must upper than Lenovo, because Lenovo tags is similar, like A853 ('A' & 3digit)
        'Motorola'      => 'Motorola.*(i1|TITANIUM)|'.
            // Android and other short motorola tag
            'Android.*\b(Motorola|\bMoto (E|G)\b|'.
            'DROIDX|DROID BIONIC|DROID4|HRI39|MOT-|Xoom|'.
            'A1260|A1680|A555|A853|A855|A953|A955|A956|'.
            'ELECTRIFY|i867|i940|'.
            'MB200|MB300|MB501|MB502|MB508|MB511|MB520|MB525|MB526|MB611|MB612|MB632|MB810|MB855|MB860|MB861|MB865|MB870|'.
            'ME501|ME502|ME511|ME525|ME600|ME632|ME722|ME811|ME860|ME863|ME865|'.
            'MT620|MT710|MT716|MT720|MT810|MT870|MT917|'.
            'WX435|WX445|'.
            'XT300|XT301|XT311|XT316|XT317|XT319|XT320|XT390|XT502|XT530|XT531|XT532|XT535|XT603|XT610|XT611|XT615|XT681|XT701|XT702|XT711|XT720|XT800|XT806|XT860|XT862|XT875|XT882|XT883|XT894|XT901|XT907|XT909|XT910|XT912|XT928|XT926|XT915|XT919|XT925|XT1021|XT1032|XT1068)\b',

        // ABC...

        'Acer'          => '\bZ160\b|\bZ500\b|Acer Z4',
        'Akai'          => '\bECO E1\b|(Akai )?GLORY.*(F2|G3|G5|L2|O2|O3|O5)',
        'Alcatel'       => 'Alcatel.*(3040G)|'.
            'ALCATEL.?ONE.?TOUCH.?([a-zA-Z0-9]+)|'.
            'ONE TOUCH \b(4033X|4015X|6012X|4015D|6012D)\b|'.
            'Android.*\b(4035D|P360X|7050Y|6050Y|6036Y|4018D|4032X|5050Y|4018X|6037Y|6016X)\b',
        'AllView'       => '\bALLVIEW.*(Impera|A4ALL|P5|A6)|'.
            'Android.*(\bALLVIEW|A4You|P5 Symbol|V1_Viper)',
        // Alps are samsung clones?
        'Alps'          => 'discovery89_tb_jb2|v89_gq3002sc|706_v92_jbla_fhd|rmt89_tb_jb2|c201v82ds_jbl_3mf5_qhd|e1902_v77_jblw005',

        'Amazon'        => 'SD4930UR',
        'Amoi'          => 'Android.*\b(Amoi)\b',
        'Archos'        => 'Archos 50(b)? (Platinum|Neon)',

        'Asus'          => 'PadFone.*Mobile|'.
            'Asus.*(Galaxy|T00N|TF300T|TF700T|T00J)|'.
            'Android.*(\bK01A\b|\bK01E\b|\bK007\b|\bK012\b|\bK013\b)',

        'BeexMobile'    => 'Beex (Flare|Conquest|DUO|Freedom|Light|Luna|M2D|M2G|M4|M45|M5|Massive|Octagon|Senior|Legend|M1D|M1G)',
        'BlackBerry'    => 'BlackBerry|\bBB10\b|rim[0-9]+',
        'BLU'           => 'Studio 5.3s',

        'Cat'           => 'Android.*(\bB15(Q)?\b)',
        'ConCorde'      => 'ConCorde.?Smartphone.?(5005.?NFC|5001|6500)',
        'Cubot'         => 'Android.*(GT95|GT72+)',
        'Cynus'         => 'Cynus T5',

        'Dell'          => 'Dell.*(Aero|Venue|Venue Pro|Flash|Smoke|Mini 3iX)|'.
            'Android.*(XCD28|XCD35|\b001DL\b|\b101DL\b|\bGS01\b)',
        'Doogee'        => 'Android.*(TURBO DG2014|DG800|Titans2_DG700|DAGGER DG550E|DAGGER DG550|VOYAGER2 DG310|DOOGEE-TITANS-DG150)',

        'Eboda'         => 'Storm X450 II|Storm X450',
        'Elephone'      => 'Elephone.*\b(G7|G6|G4|P8000|P7000|P6000|P2000)\b',
        'Evolveo'       => 'StrongPhoneQ4',

        'Fly'           => '\bIQ[0-9]+\b',

        'GigaByte'      => 'GSmart [a-zA-Z]+ [a-zA-Z][0-9]',
        'GoClever'      => 'GCFONE 570Q|INSIGNIA_550i',

        'Haier'         => 'Haier HW-W910|Android.*(\bW716S\b)',
        'HP'            => 'HP Slate 6 Voice Tab',
        'HTC'           => 'HTC.*(Sensation|Evo|Vision|Explorer|One.?(X|Mini|M8)?|Wildfire S A510e|M8w|6800|8100|8900|A7272|S510e|C110e|Legend|Desire.?(310|500|610|C|S|X)?|T8282|M8x|One_M8|M8Sx|OneXplus|WildfireS_A510e|M8MINx)|'.
            'Android.*\b(EVO|T-Mobile G1|Z520m|M9|SensationXL_Beats_X315e|ADR6200|ADR6400L|ADR6425|001HT|Inspire 4G|APX515CKT|Qtek9090|APA9292KT|HD_mini|Sensation.*Z710e|PG86100|Z715e|Desire.*(A8181|HD))\b',
        'HUAWEI'        => 'HUAWEI.?[a-zA-Z0-9\-]+|'.
            'Android.*(U8500|H60-L04|CHM-U01)',

        'iMobile'        => 'i-mobile \b(IQ|i-STYLE|idea|ZAA|Hitz)\b',
        'INQ'           => '\bINQ\b',
        'iPhone'        => '\b(iPhone|iPod)\b',

        'Jiayu'         => 'JY-(G2|G3|G5)|JiaYu.?(G4S|S2|G3)',

        'Kazam'         => 'Tornado 348',
        'Kingzone'      => 'K1 turbo',

        'Leagoo'        => '\b(Lead \d(S)?)\b',
        'Lenovo'        => 'Lenovo.?[a-zA-Z][0-9]{2,3}([a-zA-Z])?|'.
            'Lenovo.*X2-[a-zA-Z]U|'.
            'Lenovo.*(Z2|A5000|A6000)|'.
            'Android.*(S850c)',

        'LG'            => 'LG[- ]?[a-zA-Z0-9]+|Optimus 4X HD',

        'M-Tech'        => '\b(aTAB5C|aTAB3502D|Atab403|aTAB5|aTAB503D)\b',
        'Mann'          => '\bzug3|zug 3\b',
        'MediaTek'      => 'Android.*(\bMT6572\b)',
        'Medion'        => 'Android.*(\bP4502\b)',
        'Meizu'         => 'Android.*(\bm1 note\b)',
        'Mobiwire'      => 'Android.*(\bAhiga\b)',
        'Modecom'       => 'Xino Z25 X2',
        'MPIE'          => 'Android.*(\b909T\b)',

        'Navon'         => 'Android.*\b(M402|M450|S400_01|M502|D402|D501|D500|M500|F550)\b',
        'Neken'         => 'N6',
        'Nintendo'      => 'Nintendo 3DS',
        'Nokia'         => 'Nokia.*(X|XL|C5|5800d|Lumia [0-9]+|Lumia)|'.
            '\bNokia([0-9])?\b|'.
            'Windows Phone'.
            'Android.*\b(Series40|Series60|S60|N900)\b',


        'OnePlus'       => 'Android.*(\bA0001\b)',
        'Orange'        => 'Orange Yomi',
        'Overmax'       => 'OV-Vertis-(01|02)|OV_V(\d)',

        'Palm'          => 'PalmSource|\bPalm\b', // avantgo|blazer|elaine|hiptop|plucker|xiino ;
        'Pantech'       => 'PANTECH.*(IM-|VEGA|PT|P|ADR|CDM|TXT|IS|C)[0-9]+',
        'Philips'       => 'Philips S308',
        'Prestigio'     => 'Prestigio|(PSP|PAP)[0-9]{4}(DUO)?',

        'Qilive'        => 'Qilive (40|50|53)|'.
            'Android.*\b(Q4688)\b',

        'SAGEM'         => 'SAGEM',
        'Samsung'       => '(Samsung|Android).*(T-|I-|GT-|SM-|SGH-|BGT-|SCH-|SHW-|SPH-|SWC-|SHV-)[a-zA-Z0-9]+|'.
            'Nexus.?(One|S|\d)+|Galaxy.*Nexus|'.
            'Samsung.*Omnia[0-9]?|Samsung|I9500',
        'SimValley'     => '\b(SP-80|XT-930|SX-340|XT-930|SX-310|SP-360|SP60|SPT-800|SP-120|SPT-800|SP-140|SPX-5|SPX-8|SP-100|SPX-8|SPX-12)\b',
        'SonyEricsson'  => 'SonyEricsson.?[a-zA-Z0-9]{2,6}',
        'Sony'          => 'Sony.?[a-zA-Z0-9]{2,6}|'.
            'Android.*\b(C1505|C5303|C1905|C6603|C6903|C2105|C6903|C1605|C2104|C6833|C5503|C2005|C2305|'.
            'D5803|D5503|D6503|D2303|D2303|D2005|D6603|D5303|D5103|D2203|D6653|D2403|D2302|'.
            'E2003|E2105|E2303|E2104|'.
            'LT26i|LT26ii|LT26i|LT26ii|LT22i|LT25i|LT30p|LT28h|LT26w|LT18i|'.
            'SK17i|ST25a|ST26i|ST21i|ST23i|ST25i|ST17i|ST18i|ST27i|ST15i|'.
            'Xperia (SP|Acro S))\b',

        'Telenor'       => 'Telenor.?One.?Touch.?[a-zA-Z]?|'.
            'TELENOR SMART TOUCH MINI|Telenor_Smart_HD',
        'THL'           => 'Thl[ _]?(W200S|4000|T6S|T6 pro|5000|T11|W8|W200S)',

        'Utok'          => 'Android.*(\b401D\b)',

        'Vega'          => '\bQ7108\b',
        'Vertu'         => 'Vertu.*\b(Ltd|Ascent|Ayxta|Constellation(F|Quest)|Monika|Signature)\b',
        'Vodafone'      => 'vodafone|VF-895N|VF685|VF695',

        'WayTeq'        => 'TALK_5H',
        'Wiko'          => '\b(KITE 4G|HIGHWAY|GETAWAY|STAIRWAY|DARKSIDE|DARKFULL|DARKNIGHT|DARKMOON|SLIDE|WAX 4G|RAINBOW|BLOOM|SUNSET|GOA|LENNY|BARRY|IGGY|OZZY|CINK FIVE|CINK PEAX|CINK PEAX 2|CINK SLIM|CINK SLIM 2|CINK +|CINK KING|CINK PEAX|CINK SLIM|SUBLIM)\b',
        'Wolfgang'      => 'AT-B24D|AT-AS50HD|AT-AS40W|AT-AS55HD|AT-AS45q2|AT-B26D|AT-AS50Q',

        'Xiaomi'        => 'Android.*(\bHM 1SW\b|\bHM 1S\b|\bMI 3W\b|\bMI 3W\b|\bMI 2S\b|HM NOTE 1LTE)',

        'Zopo'          => 'Android.*(\bZP999\b)|ZOPO 9xxQuad',
        'ZTE'           => '\b(KIS PLUS|Blade|ZTE-SKATE|Kis|Grand X In|Cosmote (Xplore|Smart Share)|X9180)\b|'.
            'ZTE (V795|V970|V807|Q705U)',

        //
        'Opera?'        => 'Android.*(Opera Mobi|Opera Mini)',
        'N3?'           => 'Android.*(\bN3\b Build)',

        // TODO: FIZZ|V9|V2|X8|W100|i6|Q4 Android|01_v89_gq2008s_89t|Q7901L2C|Discovery|Rock HU|AJM|I50|MT6582_TD|Skate
        'GenericPhone'  => '\b(Tapatalk|PDA|mmp|pocket|psp|symbian|Smartphone|smartfon|treo|up.browser|up.link|wap|MAUI.*WAP.*Browser|Android; Mobile)\b',
    );

    /**
     * pattern for detecting tablets
     * @var array
     */
    private $patternTablet = array(
        'iPad'              => 'iPad|iPad.*Mobile',
        // Removed |^.*Android.*Nexus(?!(?:Mobile).)*$
        // @see #442
        'NexusTablet'       => 'Android.*Nexus[\s]+(7|9|10)',
        'Alcatel'           => 'onetouch EVO7|ONE TOUCH EVO7HD|ONE TOUCH TAB 8HD',
        'SamsungTablet'     => 'SAMSUNG.*Tablet|Galaxy.*Tab|SC-01C|GT-P1000|GT-P1003|GT-P1010|GT-P3105|GT-P6210|GT-P6800|GT-P6810|GT-P7100|GT-P7300|GT-P7310|GT-P7500|GT-P7510|SCH-I800|SCH-I815|SCH-I905|SGH-I957|SGH-I987|SGH-T849|SGH-T859|SGH-T869|SPH-P100|GT-P3100|GT-P3108|GT-P3110|GT-P5100|GT-P5110|GT-P6200|GT-P7320|GT-P7511|GT-N8000|GT-P8510|SGH-I497|SPH-P500|SGH-T779|SCH-I705|SCH-I915|GT-N8013|GT-P3113|GT-P5113|GT-P8110|GT-N8010|GT-N8005|GT-N8020|GT-P1013|GT-P6201|GT-P7501|GT-N5100|GT-N5105|GT-N5110|SHV-E140K|SHV-E140L|SHV-E140S|SHV-E150S|SHV-E230K|SHV-E230L|SHV-E230S|SHW-M180K|SHW-M180L|SHW-M180S|SHW-M180W|SHW-M300W|SHW-M305W|SHW-M380K|SHW-M380S|SHW-M380W|SHW-M430W|SHW-M480K|SHW-M480S|SHW-M480W|SHW-M485W|SHW-M486W|SHW-M500W|GT-I9228|SCH-P739|SCH-I925|GT-I9200|GT-P5200|GT-P5210|GT-P5210X|SM-T311|SM-T310|SM-T310X|SM-T210|SM-T210R|SM-T211|SM-P600|SM-P601|SM-P605|SM-P900|SM-P901|SM-T217|SM-T217A|SM-T217S|SM-P6000|SM-T3100|SGH-I467|XE500|SM-T110|GT-P5220|GT-I9200X|GT-N5110X|GT-N5120|SM-P905|SM-T111|SM-T2105|SM-T315|SM-T320|SM-T320X|SM-T321|SM-T520|SM-T525|SM-T530NU|SM-T230NU|SM-T330NU|SM-T900|XE500T1C|SM-P605V|SM-P905V|SM-T337V|SM-T537V|SM-T707V|SM-T807V|SM-P600X|SM-P900X|SM-T210X|SM-T230|SM-T230X|SM-T325|GT-P7503|SM-T531|SM-T330|SM-T530|SM-T705|SM-T705C|SM-T535|SM-T331|SM-T800|SM-T700|SM-T537|SM-T807|SM-P907A|SM-T337A|SM-T537A|SM-T707A|SM-T807A|SM-T237|SM-T807P|SM-P607T|SM-T217T|SM-T337T|SM-T807T|SM-T116NQ|SM-P550|SM-T350|SM-T550|SM-T9000|SM-P9000|SM-T705Y|SM-T805|GT-P3113|SM-T710|SM-T810|SM-T360|SM-T533', // SCH-P709|SCH-P729|SM-T2558|GT-I9205 - Samsung Mega - treat them like a regular phone.
        // http://docs.aws.amazon.com/silk/latest/developerguide/user-agent.html
        'Kindle'            => 'Kindle|Silk.*Accelerated|Android.*\b(KFOT|KFTT|KFJWI|KFJWA|KFOTE|KFSOWI|KFTHWI|KFTHWA|KFAPWI|KFAPWA|WFJWAE|KFSAWA|KFSAWI|KFASWI)\b',
        'Lark'              => 'Lark Ulitmate 7i',
        'BeexMobile'        => 'Cobra (SD|HD)|(E2|D2) Tablet|Flash|Minibee|Rock|Rainbow|S1|S2|S4|S5|Thunderbolt',
        // Only the Surface tablets with Windows RT are considered mobile.
        // http://msdn.microsoft.com/en-us/library/ie/hh920767(v=vs.85).aspx
        'SurfaceTablet'     => 'Windows NT [0-9.]+; ARM;.*(Tablet|ARMBJS)',
        // http://shopping1.hp.com/is-bin/INTERSHOP.enfinity/WFS/WW-USSMBPublicStore-Site/en_US/-/USD/ViewStandardCatalog-Browse?CatalogCategoryID=JfIQ7EN5lqMAAAEyDcJUDwMT
        'HPTablet'          => 'HP Slate (7|8|10)|HP ElitePad 900|hp-tablet|EliteBook.*Touch|HP 8|Slate 21|HP SlateBook 10',
        // Watch out for PadFone, see #132.
        // http://www.asus.com/de/Tablets_Mobile/Memo_Pad_Products/
        'AsusTablet'        => '^.*PadFone((?!Mobile).)*$|'.
            '\b(Transformer|TF101|K00Z|TF101G|TF300T|TF300TG|TF300TL|TF700T|TF700KL|TF701T|TF810C|ME171|ME301T|ME302C|ME371MG|ME370T|ME372MG|ME172V|ME173X|ME400C|Slider SL101|K00F|K00C|K00E|K00L|TX201LA|ME176C|ME102A|M80TA|ME372CL|ME560CG|ME372CG|ME302KL|K010|K011|K017|ME572C|ME103K|ME170C|ME171C|ME70C|ME581C|ME581CL|ME8510C|ME181C)\b',
        'BlackBerryTablet'  => 'PlayBook|RIM Tablet',
        'Colorovo'          => 'CTLite8 2.0',
        'HTCtablet'         => 'HTC_Flyer_P512|HTC Flyer|HTC Jetstream|HTC-P715a|HTC EVO View 4G|PG41200|PG09410',
        'Kiano'             => 'KianoIntelect83G',
        'MotorolaTablet'    => 'xoom|sholest|MZ615|MZ605|MZ505|MZ601|MZ602|MZ603|MZ604|MZ606|MZ607|MZ608|MZ609|MZ615|MZ616|MZ617',
        'NookTablet'        => 'Android.*Nook|NookColor|nook browser|BNRV200|BNRV200A|BNTV250|BNTV250A|BNTV400|BNTV600|LogicPD Zoom2',
        // http://www.acer.ro/ac/ro/RO/content/drivers
        // http://www.packardbell.co.uk/pb/en/GB/content/download (Packard Bell is part of Acer)
        // http://us.acer.com/ac/en/US/content/group/tablets
        // http://www.acer.de/ac/de/DE/content/models/tablets/
        // Can conflict with Micromax and Motorola phones codes.
        'AcerTablet'        => 'Android.*; \b(A100|A101|A110|A200|A210|A211|A500|A501|A510|A511|A700|A701|W500|W500P|W501|W501P|W510|W511|W700|G100|G100W|B1-A71|B1-710|B1-711|A1-810|A1-811|A1-830)\b|W3-810|\bA3-A10\b|\bA3-A11\b',
        // http://eu.computers.toshiba-europe.com/innovation/family/Tablets/1098744/banner_id/tablet_footerlink/
        // http://us.toshiba.com/tablets/tablet-finder
        // http://www.toshiba.co.jp/regza/tablet/
        'ToshibaTablet'     => 'Android.*(AT10-A|AT100|AT105|AT200|AT205|AT270|AT275|AT300|AT305|AT1S5|AT500|AT570|AT700|AT830)|TOSHIBA.*FOLIO',
        // http://www.nttdocomo.co.jp/english/service/developer/smart_phone/technical_info/spec/index.html
        // http://www.lg.com/us/tablets
        'LGTablet'          => '\bL-06C|LG-V909|LG-V900|LG-V700|LG-V510|LG-V500|LG-V410|LG-V400|LG-VK810\b',
        'GigaByte'          => 'GSmart7Tab',
        'FujitsuTablet'     => 'Android.*\b(F-01D|F-02F|F-05E|F-10D|M532|Q572)\b',
        'Alcor'             => '\bD785M\b|\bQ933RS\b|Alcor.*(Zest|Access).*Q[0-9]{3}([a-zA-Z]{1,2})?|Access_D746I|Access-O719R|Access Q881M',
        // Prestigio Tablets http://www.prestigio.com/support
        'PrestigioTablet'   => 'Android.*\b((PMP|PER|PMT)([_0-9a-zA-Z])+|GV7777|QUANTUM_785)\b',
        // http://support.lenovo.com/en_GB/downloads/default.page?#
        'LenovoTablet'      => 'Idea(Tab|Pad)( A1|A10| K1|)|ThinkPad([ ]+)?Tablet|Lenovo.?[a-zA-Z][0-9]{2,3}([a-zA-Z]{1,2})?|Tab2A7-10F|Lenovo.*(TAB S8-50L|TAB 2 A7-30HC)',
        // http://www.dell.com/support/home/us/en/04/Products/tab_mob/tablets
        'DellTablet'        => 'Venue 11|Venue 8|Venue 7|Dell Streak [0-9]+',
        'Yamoto'            => 'MT92|MT102W|LT1001|Q88H',
        // http://www.yarvik.com/en/matrix/tablets/
        'YarvikTablet'      => 'Android.*\b(TAB210|TAB211|TAB224|TAB250|TAB260|TAB264|TAB310|TAB360|TAB364|TAB410|TAB411|TAB420|TAB424|TAB450|TAB460|TAB461|TAB464|TAB465|TAB467|TAB468|TAB07-100|TAB07-101|TAB07-150|TAB07-151|TAB07-152|TAB07-200|TAB07-201-3G|TAB07-210|TAB07-211|TAB07-212|TAB07-214|TAB07-220|TAB07-400|TAB07-485|TAB08-150|TAB08-200|TAB08-201-3G|TAB08-201-30|TAB09-100|TAB09-211|TAB09-410|TAB10-150|TAB10-201|TAB10-211|TAB10-400|TAB10-410|TAB13-201|TAB274EUK|TAB275EUK|TAB374EUK|TAB462EUK|TAB474EUK|TAB9-200)\b',
        'MedionTablet'      => 'Android.*\bOYO\b|LIFE.*(P9212|P9514|P9516|S9512)|LIFETAB',
        'ArnovaTablet'      => 'AN10G2|AN7bG3|AN7fG3|AN8G3|AN8cG3|AN7G3|AN9G3|AN7dG3|AN7dG3ST|AN7dG3ChildPad|AN10bG3|AN10bG3DT|AN9G2',
        // http://www.intenso.de/kategorie_en.php?kategorie=33
        // @todo: http://www.nbhkdz.com/read/b8e64202f92a2df129126bff.html - investigate
        'IntensoTablet'     => 'INM8002KP|INM1010FP|INM805ND|Intenso Tab|TAB1004',
        // IRU.ru Tablets http://www.iru.ru/catalog/soho/planetable/
        'IRUTablet'         => 'M702pro',
        'Akai'              => 'TAB-7800',
        'Polaroid'          => 'Android.*(\bMIDC407\b)',
        'MegafonTablet'     => 'MegaFon V9|\bZTE V9\b|Android.*\bMT7A\b',
        // http://www.e-boda.ro/tablete-pc.html
        'EbodaTablet'       => 'E-Boda (Supreme|Impresspeed|Izzycomm|Essential)',
        // http://www.allview.ro/produse/droseries/lista-tablete-pc/
        'AllViewTablet'     => 'Allview.*\b(Viva|Alldro|City|Speed|All TV|Frenzy|Quasar|Shine|TX1|AX1|AX2)\b|'.
            'Android.*(VivaQ7Satellite|SPEED_QUAD)',
        // http://wiki.archosfans.com/index.php?title=Main_Page
        'ArchosTablet'      => '\b(101G9|80G9|A101IT)\b|Archos5|\bARCHOS (70|79|80|90|97|101|FAMILYPAD|)(b|)(G10| Cobalt| TITANIUM(HD|)| Xenon| Neon|XSK| 2| XS 2| PLATINUM| CARBON|GAMEPAD)\b',
        'Qilive'            => 'Qilive 7|Qilive 97R',
        // http://www.ainol.com/plugin.php?identifier=ainol&module=product
        'AinolTablet'       => 'NOVO7|NOVO8|NOVO10|Novo7Aurora|Novo7Basic|NOVO7PALADIN|novo9-Spark',
        // @todo: inspect http://esupport.sony.com/US/p/select-system.pl?DIRECTOR=DRIVER
        // Readers http://www.atsuhiro-me.net/ebook/sony-reader/sony-reader-web-browser
        // http://www.sony.jp/support/tablet/
        'SonyTablet'        => 'Sony.*Tablet|Xperia Tablet|Sony Tablet S|SO-03E|SGPT12|SGPT13|SGPT114|SGPT121|SGPT122|SGPT123|SGPT111|SGPT112|SGPT113|SGPT131|SGPT132|SGPT133|SGPT211|SGPT212|SGPT213|SGP311|SGP312|SGP321|EBRD1101|EBRD1102|EBRD1201|SGP351|SGP341|SGP511|SGP512|SGP521|SGP541|SGP551|SGP621|SGP612|SOT31|SGP611',
        // http://www.support.philips.com/support/catalog/worldproducts.jsp?userLanguage=en&userCountry=cn&categoryid=3G_LTE_TABLET_SU_CN_CARE&title=3G%20tablets%20/%20LTE%20range&_dyncharset=UTF-8
        'PhilipsTablet'     => '\b(PI2010|PI3000|PI3100|PI3105|PI3110|PI3205|PI3210|PI3900|PI4010|PI7000|PI7100)\b',
        // db + http://www.cube-tablet.com/buy-products.html
        'CubeTablet'        => 'Android.*(K8GT|U9GT|U10GT|U16GT|U17GT|U18GT|U19GT|U20GT|U23GT|U30GT|U51GT)|CUBE U8GT',
        // http://www.cobyusa.com/?p=pcat&pcat_id=3001
        'CobyTablet'        => 'MID1042|MID1045|MID1125|MID1126|MID7012|MID7014|MID7015|MID7034|MID7035|MID7036|MID7042|MID7048|MID7127|MID8042|MID8048|MID8127|MID9042|MID9740|MID9742|MID7022|MID7010',
        // http://www.match.net.cn/products.asp
        'MIDTablet'         => 'M9701|M9000|M9100|M806|M1052|M806|T703|MID701|MID713|MID710|MID727|MID760|MID830|MID728|MID933|MID125|MID810|MID732|MID120|MID930|MID800|MID731|MID900|MID100|MID820|MID735|MID980|MID130|MID833|MID737|MID960|MID135|MID860|MID736|MID140|MID930|MID835|MID733',
        // http://www.msi.com/support
        // @todo Research the Windows Tablets.
        'MSITablet'         => 'MSI \b(Primo 73K|Primo 73L|Primo 81L|Primo 77|Primo 93|Primo 75|Primo 76|Primo 73|Primo 81|Primo 91|Primo 90|Enjoy 71|Enjoy 7|Enjoy 10)\b',
        'M-Tech'            => 'aTAB7DCI|\bATAB\b',
        // @todo http://www.kyoceramobile.com/support/drivers/
        //    'KyoceraTablet' => null,
        // @todo http://intexuae.com/index.php/category/mobile-devices/tablets-products/
        //    'IntextTablet' => null,
        // http://pdadb.net/index.php?m=pdalist&list=SMiT (NoName Chinese Tablets)
        // http://www.imp3.net/14/show.php?itemid=20454
        'SMiTTablet'        => 'Android.*(\bMID\b|MID-560|MTV-T1200|MTV-PND531|MTV-P1101|MTV-PND530)',
        // http://www.rock-chips.com/index.php?do=prod&pid=2
        'RockChipTablet'    => 'Android.*(RK2818|RK2808A|RK2918|RK3066)|RK2738|RK2808A',
        // http://www.fly-phone.com/devices/tablets/ ; http://www.fly-phone.com/service/
        'FlyTablet'         => 'IQ310|Fly Vision',
        // http://www.bqreaders.com/gb/tablets-prices-sale.html
        'bqTablet'          => 'Android.*(bq)?.*(Elcano|Curie|Edison|Maxwell|Kepler|Pascal|Tesla|Hypatia|Platon|Newton|Livingstone|Cervantes|Avant|Aquaris E10)|Maxwell.*Lite|Maxwell.*Plus',
        // http://www.huaweidevice.com/worldwide/productFamily.do?method=index&directoryId=5011&treeId=3290
        // http://www.huaweidevice.com/worldwide/downloadCenter.do?method=index&directoryId=3372&treeId=0&tb=1&type=software (including legacy tablets)
        'HuaweiTablet'      => 'MediaPad|MediaPad 7 Youth|IDEOS S7|S7-201c|S7-202u|S7-101|S7-103|S7-104|S7-105|S7-106|S7-201|S7-Slim',
        // Nec or Medias Tab
        'NecTablet'         => '\bN-06D|\bN-08D',
        'Gigaset'           => 'Gigaset QV830',
        // Pantech Tablets: http://www.pantechusa.com/phones/
        'PantechTablet'     => 'Pantech.*P4100',
        'BluePanther'       => 'Panther|voyager GT',
        // Broncho Tablets: http://www.broncho.cn/ (hard to find)
        'BronchoTablet'     => 'Broncho.*(N701|N708|N802|a710)',
        // http://versusuk.com/support.html
        'VersusTablet'      => 'TOUCHPAD.*[78910]|\bTOUCHTAB\b',
        // http://www.zync.in/index.php/our-products/tablet-phablets
        'ZyncTablet'        => 'z1000|Z99 2G|z99|z930|z999|z990|z909|Z919|z900',
        // http://www.positivoinformatica.com.br/www/pessoal/tablet-ypy/
        'PositivoTablet'    => 'TB07STA|TB10STA|TB07FTA|TB10FTA',
        // https://www.nabitablet.com/
        'NabiTablet'        => 'Android.*\bNabi',
        'KoboTablet'        => 'Kobo Touch|\bK080\b|\bVox\b Build|\bArc\b Build',
        // French Danew Tablets http://www.danew.com/produits-tablette.php
        'DanewTablet'       => 'DSlide.*\b(700|701R|702|703R|704|802|970|971|972|973|974|1010|1012)\b',
        'Navon'             => 'Navon Explorer 3G|Raptor_(\d)|HFM7002KD|HFM752HCF',
        // Texet Tablets and Readers http://www.texet.ru/tablet/
        'TexetTablet'       => 'NaviPad|TB-772A|TM-7045|TM-7055|TM-9750|TM-7016|TM-7024|TM-7026|TM-7041|TM-7043|TM-7047|TM-8041|TM-9741|TM-9747|TM-9748|TM-9751|TM-7022|TM-7021|TM-7020|TM-7011|TM-7010|TM-7023|TM-7025|TM-7037W|TM-7038W|TM-7027W|TM-9720|TM-9725|TM-9737W|TM-1020|TM-9738W|TM-9740|TM-9743W|TB-807A|TB-771A|TB-727A|TB-725A|TB-719A|TB-823A|TB-805A|TB-723A|TB-715A|TB-707A|TB-705A|TB-709A|TB-711A|TB-890HD|TB-880HD|TB-790HD|TB-780HD|TB-770HD|TB-721HD|TB-710HD|TB-434HD|TB-860HD|TB-840HD|TB-760HD|TB-750HD|TB-740HD|TB-730HD|TB-722HD|TB-720HD|TB-700HD|TB-500HD|TB-470HD|TB-431HD|TB-430HD|TB-506|TB-504|TB-446|TB-436|TB-416|TB-146SE|TB-126SE',
        // Avoid detecting 'PLAYSTATION 3' as mobile.
        'PlaystationTablet' => 'Playstation.*(Portable|Vita)',
        'MyAudio'           => 'Android.*\b(704T|crane-evb|Power SD)\b|My Audio',
        // http://www.trekstor.de/surftabs.html
        'TrekstorTablet'    => 'ST10416-1|VT10416-1|ST70408-1|ST702xx-1|ST702xx-2|ST80208|ST97216|ST70104-2|VT10416-2|ST10216-2A|SurfTab',
        // http://www.pyleaudio.com/Products.aspx?%2fproducts%2fPersonal-Electronics%2fTablets
        'PyleAudioTablet'   => '\b(PTBL10CEU|PTBL10C|PTBL72BC|PTBL72BCEU|PTBL7CEU|PTBL7C|PTBL92BC|PTBL92BCEU|PTBL9CEU|PTBL9CUK|PTBL9C)\b',
        // http://www.advandigital.com/index.php?link=content-product&jns=JP001
        // because of the short codenames we have to include whitespaces to reduce the possible conflicts.
        'AdvanTablet'       => 'Android.* \b(E3A|T3X|T5C|T5B|T3E|T3C|T3B|T1J|T1F|T2A|T1H|T1i|E1C|T1-E|T5-A|T4|E1-B|T2Ci|T1-B|T1-D|O1-A|E1-A|T1-A|T3A|T4i)\b ',
        // http://www.danytech.com/category/tablet-pc
        'DanyTechTablet' => 'Genius Tab G3|Genius Tab S2|Genius Tab Q3|Genius Tab G4|Genius Tab Q4|Genius Tab G-II|Genius TAB GII|Genius TAB GIII|Genius Tab S1',
        // http://www.galapad.net/product.html
        'GalapadTablet'     => 'Android.*\bG1\b',
        // http://www.micromaxinfo.com/tablet/funbook
        'MicromaxTablet'    => 'Funbook|Micromax.*\b(P250|P560|P360|P362|P600|P300|P350|P500|P275)\b',
        // http://www.karbonnmobiles.com/products_tablet.php
        'KarbonnTablet'     => 'Android.*\b(A39|A37|A34|ST8|ST10|ST7|Smart Tab3|Smart Tab2)\b',
        // http://www.myallfine.com/Products.asp
        'AllFineTablet'     => 'Fine7 Genius|Fine7 Shine|Fine7 Air|Fine8 Style|Fine9 More|Fine10 Joy|Fine11 Wide',
        // http://www.proscanvideo.com/products-search.asp?itemClass=TABLET&itemnmbr=
        'PROSCANTablet'     => '\b(PEM63|PLT1023G|PLT1041|PLT1044|PLT1044G|PLT1091|PLT4311|PLT4311PL|PLT4315|PLT7030|PLT7033|PLT7033D|PLT7035|PLT7035D|PLT7044K|PLT7045K|PLT7045KB|PLT7071KG|PLT7072|PLT7223G|PLT7225G|PLT7777G|PLT7810K|PLT7849G|PLT7851G|PLT7852G|PLT8015|PLT8031|PLT8034|PLT8036|PLT8080K|PLT8082|PLT8088|PLT8223G|PLT8234G|PLT8235G|PLT8816K|PLT9011|PLT9045K|PLT9233G|PLT9735|PLT9760G|PLT9770G)\b',
        // http://www.yonesnav.com/products/products.php
        'YONESTablet' => 'BQ1078|BC1003|BC1077|RK9702|BC9730|BC9001|IT9001|BC7008|BC7010|BC708|BC728|BC7012|BC7030|BC7027|BC7026',
        // http://www.cjshowroom.com/eproducts.aspx?classcode=004001001
        // China manufacturer makes tablets for different small brands (eg. http://www.zeepad.net/index.html)
        'ChangJiaTablet'    => 'TPC7102|TPC7103|TPC7105|TPC7106|TPC7107|TPC7201|TPC7203|TPC7205|TPC7210|TPC7708|TPC7709|TPC7712|TPC7110|TPC8101|TPC8103|TPC8105|TPC8106|TPC8203|TPC8205|TPC8503|TPC9106|TPC9701|TPC97101|TPC97103|TPC97105|TPC97106|TPC97111|TPC97113|TPC97203|TPC97603|TPC97809|TPC97205|TPC10101|TPC10103|TPC10106|TPC10111|TPC10203|TPC10205|TPC10503',
        // http://www.gloryunion.cn/products.asp
        // http://www.allwinnertech.com/en/apply/mobile.html
        // http://www.ptcl.com.pk/pd_content.php?pd_id=284 (EVOTAB)
        // @todo: Softwiner tablets?
        // aka. Cute or Cool tablets. Not sure yet, must research to avoid collisions.
        'GUTablet'          => 'TX-A1301|TX-M9002|Q702|kf026', // A12R|D75A|D77|D79|R83|A95|A106C|R15|A75|A76|D71|D72|R71|R73|R77|D82|R85|D92|A97|D92|R91|A10F|A77F|W71F|A78F|W78F|W81F|A97F|W91F|W97F|R16G|C72|C73E|K72|K73|R96G
        // http://www.pointofview-online.com/showroom.php?shop_mode=product_listing&category_id=118
        'PointOfViewTablet' => 'TAB-P506|TAB-navi-7-3G-M|TAB-P517|TAB-P-527|TAB-P701|TAB-P703|TAB-P721|TAB-P731N|TAB-P741|TAB-P825|TAB-P905|TAB-P925|TAB-PR945|TAB-PL1015|TAB-P1025|TAB-PI1045|TAB-P1325|TAB-PROTAB[0-9]+|TAB-PROTAB25|TAB-PROTAB26|TAB-PROTAB27|TAB-PROTAB26XL|TAB-PROTAB2-IPS9|TAB-PROTAB30-IPS9|TAB-PROTAB25XXL|TAB-PROTAB26-IPS10|TAB-PROTAB30-IPS10',
        // http://www.overmax.pl/pl/katalog-produktow,p8/tablety,c14/
        // @todo: add more tests.
        'OvermaxTablet'     => 'OV-(SteelCore|NewBase|Basecore|Baseone|Exellen|Quattor|EduTab|Solution|ACTION|BasicTab|TeddyTab|MagicTab|Stream|TB-08|TB-09)',
        // http://hclmetablet.com/India/index.php
        'HCLTablet'         => 'HCL.*Tablet|Connect-3G-2.0|Connect-2G-2.0|ME Tablet U1|ME Tablet U2|ME Tablet G1|ME Tablet X1|ME Tablet Y2|ME Tablet Sync',
        // http://www.edigital.hu/Tablet_es_e-book_olvaso/Tablet-c18385.html
        'DPSTablet'         => 'DPS.?(Dream (\d)|Dual (\d)|iQ7|Titan (\d))',
        // http://www.visture.com/index.asp
        'VistureTablet'     => 'V97 HD|i75 3G|Visture V4( HD)?|Visture V5( HD)?|Visture V10',
        // http://www.mijncresta.nl/tablet
        'CrestaTablet'      => 'CTP(-)?810|CTP(-)?818|CTP(-)?828|CTP(-)?838|CTP(-)?888|CTP(-)?978|CTP(-)?980|CTP(-)?987|CTP(-)?988|CTP(-)?989',
        // MediaTek - http://www.mediatek.com/_en/01_products/02_proSys.php?cata_sn=1&cata1_sn=1&cata2_sn=309
        'MediatekTablet'    => '\bMT8125|MT8389|MT8135|MT8377\b',
        // Concorde tab
        'ConcordeTablet'    => 'Concorde([ ]+)?Tab|ConCorde ReadMan|Tab_Ray|KRONOS',
        // GoClever Tablets - http://www.goclever.com/uk/products,c1/tablet,c5/
        'GoCleverTablet'    => 'GOCLEVER TAB|A7GOCLEVER|M1042|M7841|M742|R1042BK|R1041|TAB A975|TAB A7842|TAB A741|TAB A741L|TAB M723G|TAB M721|TAB A1021|TAB I921|TAB R721|TAB I720|TAB T76|TAB R70|TAB R76.2|TAB R106|TAB R83.2|TAB M813G|TAB I721|GCTA722|TAB I70|TAB I71|TAB S73|TAB R73|TAB R74|TAB R93|TAB R75|TAB R76.1|TAB A73|TAB A93|TAB A93.2|TAB T72|TAB R83|TAB R974|TAB R973|TAB A101|TAB A103|TAB A104|TAB A104.2|R105BK|M713G|A972BK|TAB A971|TAB R974.2|TAB R104|TAB R83.3|TAB A1042',
        // Modecom Tablets - http://www.modecom.eu/tablets/portal/
        'ModecomTablet'     => 'FreeTAB 9000|FreeTAB 7.4|FreeTAB 7004|FreeTAB 7800|FreeTAB 2096|FreeTAB 7.5|FreeTAB 1014|FreeTAB 1001 |FreeTAB 8001|FreeTAB 9706|FreeTAB 9702|FreeTAB 7003|FreeTAB 7002|FreeTAB 1002|FreeTAB 7801|FreeTAB 1331|FreeTAB 1004|FreeTAB 8002|FreeTAB 8014|FreeTAB 9704|FreeTAB 1003',
        // Vonino Tablets - http://www.vonino.eu/tablets
        'VoninoTablet'      => '\b(Argus[ _]?S|Diamond[ _]?79HD|Emerald[ _]?78E|Luna[ _]?70C|Onyx[ _]?S|Onyx[ _]?QS|Onyx[ _]?Z|Orin[ _]?HD|Orin[ _]?S|Otis[ _]?S|SpeedStar[ _]?S|Magnet[ _]?M9|Primus[ _]?94[ _]?3G|Primus[ _]?94HD|Primus[ _]?QS|Android.*\bQ8\b|Sirius[ _]?EVO[ _]?QS|Sirius[ _]?QS|Spirit[ _]?S)\b',
        // ECS Tablets - http://www.ecs.com.tw/ECSWebSite/Product/Product_Tablet_List.aspx?CategoryID=14&MenuID=107&childid=M_107&LanID=0
        'ECSTablet'         => 'V07OT2|TM105A|S10OT1|TR10CS1',
        // Storex Tablets - http://storex.fr/espace_client/support.html
        // @note: no need to add all the tablet codes since they are guided by the first regex.
        'StorexTablet'      => 'eZee[_\']?(Tab|Go)[0-9]+|TabLC7|Looney Tunes Tab',
        // Generic Vodafone tablets.
        'VodafoneTablet'    => 'SmartTab([ ]+)?[0-9]+|SmartTabII10|SmartTabII7|VF-1497',
        // French tablets - Essentiel B http://www.boulanger.fr/tablette_tactile_e-book/tablette_tactile_essentiel_b/cl_68908.htm?multiChoiceToDelete=brand&mc_brand=essentielb
        // Aka: http://www.essentielb.fr/
        'EssentielBTablet'  => 'Smart[ \']?TAB[ ]+?[0-9]+|Family[ \']?TAB2',
        // Ross & Moor - http://ross-moor.ru/
        'RossMoorTablet'    => 'RM-790|RM-997|RMD-878G|RMD-974R|RMT-705A|RMT-701|RME-601|RMT-501|RMT-711',
        // i-mobile http://product.i-mobilephone.com/Mobile_Device
        'iMobileTablet'     => 'i-mobile i-note',
        // http://www.tolino.de/de/vergleichen/
        'TolinoTablet'      => 'tolino tab [0-9.]+|tolino shine',
        // AudioSonic - a Kmart brand
        // http://www.kmart.com.au/webapp/wcs/stores/servlet/Search?langId=-1&storeId=10701&catalogId=10001&categoryId=193001&pageSize=72&currentPage=1&searchCategory=193001%2b4294965664&sortBy=p_MaxPrice%7c1
        'AudioSonicTablet'  => '\bC-22Q|T7-QC|T-17B|T-17P\b',
        // AMPE Tablets - http://www.ampe.com.my/product-category/tablets/
        // @todo: add them gradually to avoid conflicts.
        'AMPETablet'        => 'Android.* A78 ',
        // Skk Mobile - http://skkmobile.com.ph/product_tablets.php
        'SkkTablet'         => 'Android.* (SKYPAD|PHOENIX|CYCLOPS)',
        // Tecno Mobile (only tablet) - http://www.tecno-mobile.com/index.php/product?filterby=smart&list_order=all&page=1
        'TecnoTablet'       => 'TECNO P9',
        // JXD (consoles & tablets) - http://jxd.hk/products.asp?selectclassid=009008&clsid=3
        'JXDTablet'         => 'Android.*\b(F3000|A3300|JXD5000|JXD3000|JXD2000|JXD300B|JXD300|S5800|S7800|S602b|S5110b|S7300|S5300|S602|S603|S5100|S5110|S601|S7100a|P3000F|P3000s|P101|P200s|P1000m|P200m|P9100|P1000s|S6600b|S908|P1000|P300|S18|S6600|S9100)\b',
        // i-Joy tablets - http://www.i-joy.es/en/cat/products/tablets/
        'iJoyTablet'        => 'Tablet (Spirit 7|Essentia|Galatea|Fusion|Onix 7|Landa|Titan|Scooby|Deox|Stella|Themis|Argon|Unique 7|Sygnus|Hexen|Finity 7|Cream|Cream X2|Jade|Neon 7|Neron 7|Kandy|Scape|Saphyr 7|Rebel|Biox|Rebel|Rebel 8GB|Myst|Draco 7|Myst|Tab7-004|Myst|Tadeo Jones|Tablet Boing|Arrow|Draco Dual Cam|Aurix|Mint|Amity|Revolution|Finity 9|Neon 9|T9w|Amity 4GB Dual Cam|Stone 4GB|Stone 8GB|Andromeda|Silken|X2|Andromeda II|Halley|Flame|Saphyr 9,7|Touch 8|Planet|Triton|Unique 10|Hexen 10|Memphis 4GB|Memphis 8GB|Onix 10)',
        // http://www.intracon.eu/tablet
        'FX2Tablet'         => 'FX2 PAD7|FX2 PAD10',
        'Wink'              => 'Wink Elite 7.85',
        // http://www.xoro.de/produkte/
        // @note: Might be the same brand with 'Simply tablets'
        'XoroTablet'        => 'KidsPAD 701|PAD[ ]?712|PAD[ ]?714|PAD[ ]?716|PAD[ ]?717|PAD[ ]?718|PAD[ ]?720|PAD[ ]?721|PAD[ ]?722|PAD[ ]?790|PAD[ ]?792|PAD[ ]?900|PAD[ ]?9715D|PAD[ ]?9716DR|PAD[ ]?9718DR|PAD[ ]?9719QR|PAD[ ]?9720QR|TelePAD1030|Telepad1032|TelePAD730|TelePAD731|TelePAD732|TelePAD735Q|TelePAD830|TelePAD9730|TelePAD795|MegaPAD 1331|MegaPAD 1851|MegaPAD 2151',
        // http://www1.viewsonic.com/products/computing/tablets/
        'ViewsonicTablet'   => 'ViewPad 10pi|ViewPad 10e|ViewPad 10s|ViewPad E72|ViewPad7|ViewPad E100|ViewPad 7e|ViewSonic VB733|VB100a',
        'Viewpia'           => 'TB311',
        // http://www.odys.de/web/internet-tablet_en.html
        'OdysTablet'        => 'LOOX|XENO10|ODYS[ -](Space|EVO|Xpress|NOON)|\bXELIO\b|Xelio10Pro|XELIO7PHONETAB|XELIO10EXTREME|XELIOPT2|NEO_QUAD10',
        // http://www.captiva-power.de/products.html#tablets-en
        'CaptivaTablet'     => 'CAPTIVA PAD',
        // IconBIT - http://www.iconbit.com/products/tablets/
        'IconbitTablet'     => 'NetTAB|NT-3702|NT-3702S|NT-3702S|NT-3603P|NT-3603P|NT-0704S|NT-0704S|NT-3805C|NT-3805C|NT-0806C|NT-0806C|NT-0909T|NT-0909T|NT-0907S|NT-0907S|NT-0902S|NT-0902S',
        // http://www.teclast.com/topic.php?channelID=70&topicID=140&pid=63
        'TeclastTablet'     => 'T98 4G|\bP80\b|\bX90HD\b|X98 Air|X98 Air 3G|\bX89\b|P80 3G|\bX80h\b|P98 Air|\bX89HD\b|P98 3G|\bP90HD\b|P89 3G|X98 3G|\bP70h\b|P79HD 3G|G18d 3G|\bP79HD\b|\bP89s\b|\bA88\b|\bP10HD\b|\bP19HD\b|G18 3G|\bP78HD\b|\bA78\b|\bP75\b|G17s 3G|G17h 3G|\bP85t\b|\bP90\b|\bP11\b|\bP98t\b|\bP98HD\b|\bG18d\b|\bP85s\b|\bP11HD\b|\bP88s\b|\bA80HD\b|\bA80se\b|\bA10h\b|\bP89\b|\bP78s\b|\bG18\b|\bP85\b|\bA70h\b|\bA70\b|\bG17\b|\bP18\b|\bA80s\b|\bA11s\b|\bP88HD\b|\bA80h\b|\bP76s\b|\bP76h\b|\bP98\b|\bA10HD\b|\bP78\b|\bP88\b|\bA11\b|\bA10t\b|\bP76a\b|\bP76t\b|\bP76e\b|\bP85HD\b|\bP85a\b|\bP86\b|\bP75HD\b|\bP76v\b|\bA12\b|\bP75a\b|\bA15\b|\bP76Ti\b|\bP81HD\b|\bA10\b|\bT760VE\b|\bT720HD\b|\bP76\b|\bP73\b|\bP71\b|\bP72\b|\bT720SE\b|\bC520Ti\b|\bT760\b|\bT720VE\b|T720-3GE|T720-WiFi',
        // Onda - http://www.onda-tablet.com/buy-android-onda.html?dir=desc&limit=all&order=price
        'OndaTablet'        => '\b(V975i|Vi30|VX530|V701|Vi60|V701s|Vi50|V801s|V719|Vx610w|VX610W|V819i|Vi10|VX580W|Vi10|V711s|V813|V811|V820w|V820|Vi20|V711|VI30W|V712|V891w|V972|V819w|V820w|Vi60|V820w|V711|V813s|V801|V819|V975s|V801|V819|V819|V818|V811|V712|V975m|V101w|V961w|V812|V818|V971|V971s|V919|V989|V116w|V102w|V973|Vi40)\b[\s]+',
        'JaytechTablet'     => 'TPC-PA762',
        'BlaupunktTablet'   => 'Endeavour 800NG|Endeavour 1010',
        // http://www.digma.ru/support/download/
        // @todo: Ebooks also (if requested)
        'DigmaTablet'       => '\b(iDx10|iDx9|iDx8|iDx7|iDxD7|iDxD8|iDsQ8|iDsQ7|iDsQ8|iDsD10|iDnD7|3TS804H|iDsQ11|iDj7|iDs10)\b',
        // http://www.evolioshop.com/ro/tablete-pc.html
        // http://www.evolio.ro/support/downloads_static.html?cat=2
        // @todo: Research some more
        'EvolioTablet'      => 'ARIA_Mini_wifi|Aria[ _]Mini|Evolio X10|Evolio X7|Evolio X8|\bEvotab\b|\bNeura\b|Mondo (HD|3G)',
        // @todo http://www.lavamobiles.com/tablets-data-cards
        'LavaTablet'        => 'QPAD E704|\bIvoryS\b|E-TAB IVORY|\bE-TAB\b',
        // https://www.celkonmobiles.com/?_a=categoryphones&sid=2
        'CelkonTablet'      => 'CT695|CT888|CT[\s]?910|CT7 Tab|CT9 Tab|CT3 Tab|CT2 Tab|CT1 Tab|C820|C720|\bCT-1\b',
        // http://www.wolderelectronics.com/productos/manuales-y-guias-rapidas/categoria-2-miTab
        'WolderTablet'      => 'miTab \b(DIAMOND|SPACE|BROOKLYN|NEO|FLY|MANHATTAN|FUNK|EVOLUTION|SKY|GOCAR|IRON|GENIUS|POP|MINT|EPSILON|BROADWAY|JUMP|HOP|LEGEND|NEW AGE|LINE|ADVANCE|FEEL|FOLLOW|LIKE|LINK|LIVE|THINK|FREEDOM|CHICAGO|CLEVELAND|BALTIMORE-GH|IOWA|BOSTON|SEATTLE|PHOENIX|DALLAS|IN 101|MasterChef)\b',
        'Wayteq'            => 'xTAB-7X|xTAB 7Q GPS',
        // http://www.mi.com/en
        'MiTablet'          => '\bMI PAD\b|\bHM NOTE 1W\b',
        // http://www.nbru.cn/index.html
        'NibiruTablet'      => 'Nibiru M1|Nibiru Jupiter One',
        // http://navroad.com/products/produkty/tablety/
        'NexoTablet'        => 'NEXO NOVA|NEXO 10|NEXO AVIO|NEXO FREE|NEXO GO|NEXO EVO|NEXO 3G|NEXO SMART|NEXO KIDDO|NEXO MOBI',
        // http://leader-online.com/new_site/product-category/tablets/
        // http://www.leader-online.net.au/List/Tablet
        'LeaderTablet'      => 'TBLT10Q|TBLT10I|TBL-10WDKB|TBL-10WDKBO2013|TBL-W230V2|TBL-W450|TBL-W500|SV572|TBLT7I|TBA-AC7-8G|TBLT79|TBL-8W16|TBL-10W32|TBL-10WKB|TBL-W100',
        // http://www.datawind.com/ubislate/
        'UbislateTablet'    => 'UbiSlate[\s]?7C',
        'Utok'              => 'Android.*(\b1005D\b)',
        // http://www.pocketbook-int.com/ru/support
        'PocketBookTablet'  => 'Pocketbook',
        // http://www.tesco.com/direct/hudl/
        'Hudl'              => 'Hudl HT7S3',
        // http://www.telstra.com.au/home-phone/thub-2/
        'TelstraTablet'     => 'T-Hub2',
        'GenericTablet'     => 'Android.*\b97D\b|Tablet(?!.*PC)|BNTV250A|MID-WCDMA|LogicPD Zoom2|\bA7EB\b|CatNova8|A1_07|CT704|CT1002|\bM721\b|rk30sdk|\bEVOTAB\b|M758A|ET904|ALUMIUM10|Smartfren Tab|Endeavour 1010|Tablet-PC-4|Tagi Tab|\bM6pro\b|CT1020W|arc 10HD|\bJolla\b|\bTP750\b'
    );

    /**
     * pattern for detecting OS
     * @var array
     */
    private $patternOS = array(
        'AndroidOS'         => 'Android',
        'BlackBerryOS'      => 'blackberry|\bBB10\b|rim tablet os',
        'PalmOS'            => 'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
        'SymbianOS'         => 'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
        // @reference: http://en.wikipedia.org/wiki/Windows_Mobile
        'WindowsMobileOS'   => 'Windows CE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window Mobile|Windows Phone [0-9.]+|WCE;',
        // @reference: http://en.wikipedia.org/wiki/Windows_Phone
        // http://wifeng.cn/?r=blog&a=view&id=106
        // http://nicksnettravels.builttoroam.com/post/2011/01/10/Bogus-Windows-Phone-7-User-Agent-String.aspx
        // http://msdn.microsoft.com/library/ms537503.aspx
        'WindowsPhoneOS'   => 'Windows Phone 8.1|Windows Phone 8.0|Windows Phone OS|XBLWP7|ZuneWP7|Windows NT 6.[23]; ARM;',
        'iOS'               => '\biPhone|\biPod|\biPad',
        // http://en.wikipedia.org/wiki/MeeGo
        // @todo: research MeeGo in UAs
        'MeeGoOS'           => 'MeeGo',
        // http://en.wikipedia.org/wiki/Maemo
        // @todo: research Maemo in UAs
        'MaemoOS'           => 'Maemo',
        'JavaOS'            => 'J2ME/|\bMIDP\b|\bCLDC\b', // '|Java/' produces bug #135
        'webOS'             => 'webOS|hpwOS',
        'badaOS'            => '\bBada\b',
        'BREWOS'            => 'BREW',
        'ChromeOS'          => '\bCrOS\b',
        // desktop OS patterns
        'Windows'           => 'Windows (95|98|XP|NT)|Windows',
        'Macintosh'         => 'Macintosh',
        'Linux'             => '(X11.*Linux|Linux)',
        'Other Service'     => 'mail-proxy|dalvik|SpamBayes|thebat.exedwnloadmnger|GoogleImageProxy',
    );

    /**
     * pattern for detecting OS Version
     * @var array
     */
    private $patternOSVer = array(
        'AndroidOS'         => 'Android [.0-9]+',
        'BlackBerryOS'      => 'blackberry|\bBB10\b|rim tablet os',
        'PalmOS'            => 'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
        'SymbianOS'         => 'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
        // @reference: http://en.wikipedia.org/wiki/Windows_Mobile
        'WindowsMobileOS'   => 'Windows CE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window Mobile|Windows Phone [0-9.]+|WCE;',
        // @reference: http://en.wikipedia.org/wiki/Windows_Phone
        // http://wifeng.cn/?r=blog&a=view&id=106
        // http://nicksnettravels.builttoroam.com/post/2011/01/10/Bogus-Windows-Phone-7-User-Agent-String.aspx
        // http://msdn.microsoft.com/library/ms537503.aspx
        'WindowsPhoneOS'   => 'Windows Phone 8.1|Windows Phone 8.0|Windows Phone OS|XBLWP7|ZuneWP7|Windows NT 6.[23]; ARM;',
        'iOS'               => 'OS (\d)+(_\d)+',
        // http://en.wikipedia.org/wiki/MeeGo
        // @todo: research MeeGo in UAs
        'MeeGoOS'           => 'MeeGo',
        // http://en.wikipedia.org/wiki/Maemo
        // @todo: research Maemo in UAs
        'MaemoOS'           => 'Maemo',
        'JavaOS'            => 'J2ME/|\bMIDP\b|\bCLDC\b', // '|Java/' produces bug #135
        'webOS'             => 'webOS|hpwOS',
        'badaOS'            => '\bBada\b',
        'BREWOS'            => 'BREW',
        // desktop OS patterns
        'Windows'           => 'Windows (95|98|XP|NT (\d)+\.(\d)?)|Win98',
        'Macintosh'         => 'OS X [_0-9]+',
        'Linux'             => '(Ubuntu|X11)',
        'Other Service'     => '',
    );

    /**
     * pattern for detecting browser
     * @var array
     */
    private $patternBrowser = array(
        // @reference: https://developers.google.com/chrome/mobile/docs/user-agent
        'Chrome'            => '\bCrMo\b|CriOS|Android.*Chrome/[.0-9]* (Mobile)?',
        'Dolfin'            => '\bDolfin\b',
        'Opera'             => 'Opera|Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR/[0-9.]+|Coast/[0-9.]+',
        'Skyfire'           => 'Skyfire',
        'OE'                => 'Outlook-Express|MSOffice|ms-office|Microsoft Office',
        'IE'                => 'IEMobile|MSIEMobile|Trident|MSIE', // |Trident/[.0-9]+
        'Firefox'           => 'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|Firefox',
        'Bolt'              => 'bolt',
        'TeaShark'          => 'teashark',
        'Blazer'            => 'Blazer',
        'UCBrowser'         => 'UC.*Browser|UCWEB',
        // http://en.wikipedia.org/wiki/Midori_(web_browser)
        //'Midori'          => 'midori',
        'Tizen'             => 'Tizen',
        'baiduboxapp'       => 'baiduboxapp',
        'baidubrowser'      => 'baidubrowser',
        // https://github.com/serbanghita/Mobile-Detect/issues/7
        'DiigoBrowser'      => 'DiigoBrowser',
        // http://www.puffinbrowser.com/index.php
        'Puffin'            => 'Puffin',
        // http://mercury-browser.com/index.html
        'Mercury'           => '\bMercury\b',
        // http://en.wikipedia.org/wiki/Obigo_Browser
        'ObigoBrowser'      => 'Obigo',
        // http://en.wikipedia.org/wiki/NetFront
        'NetFront'          => 'NF-Browser',
        'Netscape'          => 'Netscape',
        'Airmail'           => 'Airmail',
        'LotusNotes'        => 'Lotus[ -]?Notes',
        // @reference: http://developer.apple.com/library/safari/#documentation/AppleApplications/Reference/SafariWebContent/OptimizingforSafarioniPhone/OptimizingforSafarioniPhone.html#//apple_ref/doc/uid/TP40006517-SW3
        'Safari'            => 'Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari|Safari',
        // @reference: http://en.wikipedia.org/wiki/Minimo
        // http://en.wikipedia.org/wiki/Vision_Mobile_Browser
        'GenericBrowser'    => 'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger',
        'Thunderbird'       => 'Thunderbird',
        'AppleWebKit'       => 'AppleWebKit',
        'Other Service'     => 'mail-proxy|dalvik|SpamBayes|thebat.exedwnloadmnger|GoogleImageProxy',
    );

    /**
     * pattern for detecting browser version
     * @var array
     */
    private $patternBrowserVer = array(
        // @reference: https://developers.google.com/chrome/mobile/docs/user-agent
        'Chrome'            => '(CriOS\/(\d)+\.(\d)+|Chrome\/(\d)+\.(\d)+)',
        'Dolfin'            => '\bDolfin\b',
        'Opera'             => 'Opera\/[.0-9]+',
        'Skyfire'           => 'Skyfire',
        'OE'                => 'Outlook-Express\/?[.0-9]*|MSOffice.?[0-9]{1,2}|Microsoft Office\/?[.0-9]+',
        'IE'                => '(IEMobile|MSIEMobile|Trident\/[.0-9]+|MSIE [.0-9]+)', // |Trident/[.0-9]+
        'Firefox'           => 'Firefox\/(\d)+(.\d)?',
        'Bolt'              => 'bolt',
        'TeaShark'          => 'teashark',
        'Blazer'            => 'Blazer',
        'UCBrowser'         => 'U[0-9]+\/([.0-9]+)',
        // http://en.wikipedia.org/wiki/Midori_(web_browser)
        //'Midori'          => 'midori',
        'Tizen'             => 'Tizen',
        'baiduboxapp'       => 'baiduboxapp',
        'baidubrowser'      => 'baidubrowser',
        // https://github.com/serbanghita/Mobile-Detect/issues/7
        'DiigoBrowser'      => 'DiigoBrowser',
        // http://www.puffinbrowser.com/index.php
        'Puffin'            => 'Puffin',
        // http://mercury-browser.com/index.html
        'Mercury'           => '\bMercury\b',
        // http://en.wikipedia.org/wiki/Obigo_Browser
        'ObigoBrowser'      => '',
        // http://en.wikipedia.org/wiki/NetFront
        'NetFront'          => 'NF-Browser',
        'Netscape'          => 'Netscape\/[0-9]+\.?[0-9]*',
        'Airmail'           => 'Airmail.?[.0-9]+',
        'LotusNotes'        => 'Lotus-Notes/[0-9\.]+',
        // @reference: http://developer.apple.com/library/safari/#documentation/AppleApplications/Reference/SafariWebContent/OptimizingforSafarioniPhone/OptimizingforSafarioniPhone.html#//apple_ref/doc/uid/TP40006517-SW3
        'Safari'             => 'Safari\/[0-9]+\.?([0-9]+)',
        // @reference: http://en.wikipedia.org/wiki/Minimo
        // http://en.wikipedia.org/wiki/Vision_Mobile_Browser
        'GenericBrowser'    => 'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger',
        'Thunderbird'       => 'Thunderbird\/(\d)+\.?(\d+)?',
        'AppleWebKit'       => 'AppleWebKit\/(\d)+\.?(\d+)?',
        'Other Service'     => '',
    );

    private $desktopOS = [
        'Windows',
        'Macintosh',
        'Linux',
        'Other Service',
    ];

    /**
     * Original UA string
     * @var string
     */
    private $originalUA = '';

    /**
     * Cleaned UA
     * @var string
     */
    private $UA = '';

    /**
     * Parsed data
     * @var array
     */
    private $parsedData = [
        'device' => null, // mobile|tablet|desktop
        'deviceBrand' => null, // LG
        'deviceType' => null, // LG-D855
        'os' => null, // Windows|Linux|IOS...
        'osVer' => null, // 5.0...
        'browser' => null, // Chrome|Firefox|InternetExplorer...
        'browserVer' => null, // 11.0...
    ];

    /**
     * Constructor
     * Set UA by param
     * @param string $UA
     */
    public function __construct($UA = ''){
        $this->setUA($UA);
    }

    /**
     * Set UA to private variable, cleaning UA and call detect method
     * @param string $UA
     * @return type
     */
    public function setUA($UA = ''){
        $this->originalUA = $UA;
        $this->cleanUA();
        return $this->detect();
    }

    /**
     * Cleaning tha main UA string, and cleaning the parsedData array
     * @return type
     */
    private function cleanUA(){
        $this->UA = trim($this->originalUA);
        foreach($this->parsedData as $k => $v){
            $this->parsedData[$k] = null;
        }
    }

    /**
     * Detect from the main UA string
     * @return bool
     */
    private function detect(){
        if ($this->UA == ''){return false;}

        ($device=$this->checkDevice($this->patternMobile, self::deviceMobile)===true ||
            $device=$this->checkDevice($this->patternTablet, self::deviceTablet)===true ||
                $device=$this->checkDevice($this->patternSmartTV, self::deviceSmartTV)===true ||
                    $device=$this->checkDevice($this->patternService, self::deviceService)===true);
        if ($device!==true){
            // set desktop device and version, version in this case is empty string ''
            $this->setDevice(self::deviceDesktop, '', '');
            unset($device);
        }

        $this->checkOS();
        // recheck the device, if parsed device is 'desktop' and parsed OS is not desktopOS, then device set to 'mobile'
        if ($this->parsedData['device'] == self::deviceDesktop && !in_array($this->parsedData['os'], $this->desktopOS)){
            $this->parsedData['device'] = self::deviceMobile;
        }

        $this->checkBrowser();

        foreach ($this->parsedData as $k => $v) {
            if ($v === null){
                return false;
            }
        }

        return true;
    }

    /**
     * Check the device patterns
     * @param $patterns
     * @param $device
     * @return bool
     */
    private function checkDevice($patterns, $device){
        foreach ($patterns as $k => $v) {
            $res = (bool) preg_match(sprintf('#%s#is', $v), $this->UA, $matches);
            if ($res === true){
                $type = (isset($matches[1]) && trim($matches[1]) != '')?$matches[1]:$matches[0];
                if (strtolower($type) == 'android' || strpos(strtolower($type), 'android') !== false){
                    $type = trim(array_pop(explode(';',$matches[0])));
                }

                $this->setDevice($device, $k, $type);
                return true;
            }
        }
        return false;
    }

    /**
     * Set the device and device version to parsedData array
     * @param $device
     * @param $brand
     * @param $type
     * @return type
     */
    private function setDevice($device, $brand, $type){
        $this->parsedData['device'] = $device;
        $this->parsedData['deviceBrand'] = $brand;
        $this->parsedData['deviceType'] = $type;
    }

    /**
     * Check the OS patterns
     * @return bool
     */
    private function checkOS(){
        foreach ($this->patternOS as $k => $v) {
            $res = (bool) preg_match(sprintf('#%s#is', $v), $this->UA, $matches);
            if ($res === true){
                // check OSVer
                $this->setOS($k, null);
                $res = (bool) preg_match(sprintf('#'.$this->patternOSVer[$k].'#is', $v), $this->UA, $matches);
                if ($res === true) {
                    $this->setOS($k, $matches[0]);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Set the OS and OS version to parsedData array
     * @param $os
     * @param $version
     * @return type
     */
    private function setOS($os, $version){
        $this->parsedData['os'] = $os;
        $this->parsedData['osVer'] = $version;
    }

    /**
     * Check the Browser patterns
     * @return bool
     */
    private function checkBrowser(){
        foreach ($this->patternBrowser as $k => $v) {
            $res = (bool) preg_match(sprintf('#%s#is', $v), $this->UA, $matches);
            if ($res === true){
                // check Browser version
                $this->setBrowser($k, null);
                $res = (bool) preg_match(sprintf('#'.$this->patternBrowserVer[$k].'#is', $v), $this->UA, $matches);
                if ($res === true) {
                    $this->setBrowser($k, $matches[0]);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Set the browser and browser version to parsedData array
     * @param $browser
     * @param $version
     * @return type
     */
    private function setBrowser($browser, $version){
        $this->parsedData['browser'] = $browser;
        $this->parsedData['browserVer'] = $version;
    }

    public function getParsedData(){
        return $this->parsedData;
    }

}
