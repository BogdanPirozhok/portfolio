/* eslint-disable */
export const textLinkFormatter = function (text) {
    const linksRegex = /<\s*a[^>]*>(.*?)<\s*\/s*a>/g;
    const justUrls = /([a-zA-Z_0-9.:/-]+\.(рф|com.ru|exnet.su|net.ru|org.ru|pp.ru|ru|ru.net|su|aero|asia|biz|com|info|mobi|name|net|org|pro|tel|travel|xxx|adygeya.ru|adygeya.su|arkhangelsk.su|balashov.su|bashkiria.ru|bashkiria.su|bir.ru|bryansk.su|cbg.ru|dagestan.ru|dagestan.su|grozny.ru|grozny.su|ivanovo.su|kalmykia.ru|kalmykia.su|kaluga.su|karelia.su|khakassia.su|krasnodar.su|kurgan.su|lenug.su|marine.ru|mordovia.ru|mordovia.su|msk.ru|msk.su|murmansk.su|mytis.ru|nalchik.ru|nalchik.su|nov.ru|nov.su|obninsk.su|penza.su|pokrovsk.su|pyatigorsk.ru|sochi.su|spb.ru|spb.su|togliatti.su|troitsk.su|tula.su|tuva.su|vladikavkaz.ru|vladikavkaz.su|vladimir.ru|vladimir.su|vologda.su|academy|accountant|accountants|actor|adult|africa|agency|airforce|apartments|app|army|art|associates|attorney|auction|audio|auto|band|bank|bar|bargains|bayern|beer|berlin|best|bet|bid|bike|bingo|bio|black|blackfriday|blog|blue|boutique|broker|brussels|build|builders|business|buzz|cab|cafe|cam|camera|camp|capital|car|cards|care|career|careers|cars|casa|cash|casino|cat|catering|center|ceo|charity|chat|cheap|christmas|church|city|claims|cleaning|click|clinic|clothing|cloud|club|coach|codes|coffee|college|cologne|community|company|computer|condos|construction|consulting|contractors|cooking|cool|coop|country|coupons|courses|credit|creditcard|cricket|cruises|dance|date|dating|deals|degree|delivery|democrat|dental|dentist|desi|design|diamonds|diet|digital|direct|directory|discount|doctor|dog|domains|download|earth|education|email|energy|engineer|engineering|enterprises|equipment|estate|events|exchange|expert|exposed|express|fail|faith|family|fans|farm|fashion|film|finance|financial|fish|fishing|fit|fitness|flights|florist|flowers|fm|football|forex|forsale|foundation|fun|fund|furniture|futbol|fyi|gallery|game|games|garden|gent|gift|gifts|gives|glass|global|gmbh|gold|golf|graphics|gratis|green|gripe|group|guide|guitars|guru|haus|healthcare|help|hiphop|hockey|holdings|holiday|horse|hospital|host|hosting|house|how|immo|immobilien|industries|ink|institute|insure|international|investments|irish|jewelry|jobs|juegos|kaufen|kim|kitchen|kiwi|land|lawyer|lease|legal|life|lighting|limited|limo|link|live|llc|loan|loans|lol|london|love|ltd|luxe|luxury|maison|management|market|marketing|mba|media|memorial|men|menu|miami|moda|moe|mom|money|mortgage|moscow|movie|navy|network|news|ninja|observer|one|onl|online|ooo|page|paris|partners|parts|party|pet|photo|photography|photos|pics|pictures|pink|pizza|plumbing|plus|poker|press|productions|promo|properties|property|protection|pub|qpon|racing|radio|radio.am|radio.fm|realty|recipes|red|rehab|reisen|rent|rentals|repair|report|republican|rest|restaurant|review|reviews|rich|rip|rocks|rodeo|run|sale|salon|sarl|school|schule|science|security|services|sex|sexy|shiksha|shoes|shop|shopping|show|singles|site|ski|soccer|social|software|solar|solutions|soy|space|sport|store|stream|studio|study|style|sucks|supplies|supply|support|surf|surgery|systems|tatar|tattoo|tax|taxi|team|tech|technology|tennis|theater|theatre|tickets|tienda|tips|tires|tirol|today|tools|top|tours|town|toys|trade|trading|training|tube|tv|university|uno|vacations|vegas|ventures|vet|viajes|video|villas|vin|vip|vision|vodka|vote|voting|voto|voyage|watch|webcam|website|wedding|wien|wiki|win|wine|work|works|world|wtf|xyz|yoga|zone|дети|москва|онлайн|орг|рус|сайт|ad|ae|af|ai|al|am|aq|as|at|aw|ax|az|ba|be|bg|bh|bi|bj|bm|bo|bs|bt|ca|cc|cd|cf|cg|ch|ci|cl|cm|cn|co|co.ao|co.bw|co.ck|co.fk|co.id|co.il|co.in|co.ke|co.ls|co.mz|co.no|co.nz|co.th|co.tz|co.uk|co.uz|co.za|co.zm|co.zw|com.ai|com.ar|com.au|com.bd|com.bn|com.br|com.cn|com.cy|com.eg|com.et|com.fj|com.gh|com.gn|com.gt|com.gu|com.hk|com.jm|com.kh|com.kw|com.lb|com.lr|com.mt|com.mv|com.ng|com.ni|com.np|com.nr|com.om|com.pa|com.pl|com.py|com.qa|com.sa|com.sb|com.sg|com.sv|com.sy|com.tr|com.tw|com.ua|com.uy|com.ve|com.vi|com.vn|com.ye|cr|cu|cx|cz|de|dj|dk|dm|do|dz|ec|ee|es|eu|fi|fo|fr|ga|gd|ge|gf|gg|gi|gl|gm|gp|gr|gs|gy|hk|hm|hn|hr|ht|hu|ie|im|in|in.ua|io|ir|is|it|je|jo|jp|kg|ki|kiev.ua|kn|kr|ky|kz|li|lk|lt|lu|lv|ly|ma|mc|md|me.uk|mg|mk|mo|mp|ms|mt|mu|mw|mx|my|na|nc|net.cn|nf|ng|nl|no|nu|nz|org.cn|org.uk|pe|ph|pk|pl|pn|pr|ps|pt|re|ro|rs|rw|sd|se|sg|si|sk|sl|sm|sn|so|sr|st|sz|tc|td|tg|tj|tk|tl|tm|tn|to|tt|tr|tw|ua|ug|uk|us|vc|vg|vn|vu|ws).*?(?=<|$))/gm;
    let deletedHrefs = [];

    let changedHrefs = text.replace(linksRegex, function (url) {
        return url.replace(/href="(.*?)"/, function(m, $1) {
            return 'href="' + ($1.includes('http') ? '' : '//') + $1 + '"';
        });
    })

    let matchedArray = changedHrefs.match(linksRegex);
    if (matchedArray && matchedArray.length) {
        matchedArray.forEach((item, index) => {
            deletedHrefs.push({
                ['##__this-link_' + index]: item
            });
            changedHrefs = changedHrefs.replace(item, '##__this-link_' + index);
        });
    }

    let detectedUrls = changedHrefs.replace(justUrls, function(url) {
        if (url.includes(' ')) {
            const arrayOfUrls = url.split(' ');
            arrayOfUrls.forEach(function(item, index, theArray) {
                theArray[index] = item.replace(justUrls, function (urlInString) {
                    return '<a target="_blank" href="' + (urlInString.includes('http') ? '' : '//') + urlInString + '">' + urlInString + '</a>';
                })
            });

            return arrayOfUrls.join(' ');
        } else {
            return '<a target="_blank" href="' + (url.includes('http') ? '' : '//') + url + '">' + url + '</a>'
        }
    });

    if (deletedHrefs.length) {
        deletedHrefs.forEach((item) => {
            detectedUrls = detectedUrls.replace(Object.keys(item)[0], item[Object.keys(item)[0]]);
        })
    }

    return detectedUrls;
}
