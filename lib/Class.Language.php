<?php
/*
 * FileName: Class.Term.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/

class Language
{
	public $lang = 'en';
	public $lib;
	public $lang_map = array('en'=>'13','de'=>'14','fr'=>'15');

	function Language(){
		$this->lib = $this-> getLib();
	}
	
	function getLib(){
		return array(
				'en'=>array(
						'LEFT_HOLIDAY_TITLE'=>'Holiday Sale',
						'LEFT_RELATED_STORES'=>'Hot Stores',
						'LEFT_RELATED_CATEGORIES'=>'Related Categories',
						'LEFT_SEND_MAIL_MSG_1'=>'Send Promo Codes to Your Inbox',
						
						
						'LEFT_NEWSLETTER_PLACEHOLDER'=>'Enter your email',
						'LEFT_NEWSLETTER_BUTTON'=>'Sign in',
						'LEFT_NEWSLETTER_MSG_1'=>'Receive Free DiscountsStory Newsletter!',
						
						
						'LEFT_SUBMIT_COUPON_TITLE'=>'Submit a Coupon',
						'LEFT_SUBMIT_COUPON_FORM_1'=>'Unknown Expiration',
						'LEFT_SUBMIT_COUPON_FORM_2'=>'Not readable? Change text.',
						'LEFT_SUBMIT_COUPON_MSG_1'=>'<strong>Thank you!</strong><br><span>Your coupon has been successfully submitted and will be reviewed for approval.</span>',
						
						'ALERT_VOTE_MSG_1'=>'Receive the latest hand-picked promo codes & deals by email !',
						'ALERT_VOTE_MSG_2'=>'Enter your email address',
						'ALERT_VOTE_MSG_3'=>'Please share with other customers if this discount worked!',
						'ALERT_VOTE_MSG_4'=>'Copy and enter the coupon code at checkout!',
						'ALERT_VOTE_MSG_5'=>'get code/deal',
						'ALERT_VOTE_MSG_6'=>'No Coupon Code Needed',
						'ALERT_VOTE_MSG_7'=>'Your discount will be automatically applied at checkout!',
						'ALERT_VOTE_MSG_8'=>'Copy and enter the coupon code at checkout!',
						
						'LIST_TITLE_1'=>'Click to get coupon code and visit store',
						'LIST_TITLE_2'=>'People also be interested in',
						'LIST_TITLE_3'=>'Sorry, there are no available promo codes for <{$TermName}> now.',
						'LIST_TITLE_4'=>'You can check out our best coupons and keep shopping.',
						'LIST_TITLE_5'=>'Haven’t found what you are looking for? Please view these international stores',
						'LIST_TITLE_6'=>'we’re detecting <br />that you are from <br /><{$item.country_name}>.',
						'LIST_TITLE_7'=>'Maybe Valid Coupons',

						'TOPIC_TITLE_1'=>'Recommended Promo Codes',
						
						'HEADER_LOGO_TITLE'=>'Promo Codes of <{$year}>',

						'GET_CODE'=>'Get Code',
						'ADD_TO_FAVORITE'=>'Add to Favorite',
						'SEARCH'=>'search',
						'SEARCH_PLACEHOLDER'=>'Enter a store name to find promo codes',
						'PROMO'=>'Promo',
						'CODES'=>'Codes',
						'CLICKS'=>'clicks',
						'SHARE'=>'Share',
						'COUPON'=>'Coupon',
						'PROMO_CODE'=>'Promo Code',
						'PROMO_CODE_L'=>'promo code',
						'PROMO_CODES'=>'Promo Codes',
						'PROMO_DETAIL'=>'Promo Detail',
						'PROMOTION_CODE'=>'Promotion Code',
						'PROMOTIONAL_CODE'=>'Promotional Code',
						'COUPON_CODE'=>'Coupon Code',
						'COUPON_CODES'=>'Coupon Codes',
						'COUPON_CODES_L'=>'coupon codes',
						'DEAL'=>'Deal',
						'DEALS'=>'Deals',
						'PROMOTION'=>'Promotion',
						'OFFER'=>'Offer',
						'OFF'=>'Off',
						'BUY_MORE_PAY_LESS'=>'Buy more pay less',
						'BOGO'=>'Bogo',
						'BNGN'=>'BNGN',
						'FREE_GIFTS'=>'Free Gifts',
						'FREE_SAMPLE'=>'Free Sample',
						'FREE_SHIPPING'=>'Free Shipping',
						'FREE_DELIVERY'=>'Free Delivery',
						'SPECIAL'=>'Special',
						'EXPIRATION_DATE'=>'Expiration Date',
						'EXPIRE_DATA'=>'Expire Date',
						'SEE_ALL'=>'See All',
						'SEE_MORE'=>'See More',
						'VOTE'=>'vote',
						'SUBSCRIBE'=>'Subscribe',
						'SUBMIT'=>'Submit',
						'VISIT'=>'Visit',
						'MORE'=>'More',
						'ABOUT'=>'About',
						'DISCOUNT'=>'Discount',
						'SAVINGS'=>'Savings',
						'LOGIN_WITH' => 'Login with', 
						'MY_FAVORITES' => 'My Favorites', 
						'FIND_YOUR_FAV_COUPONS' => 'Find your favorite coupons', 
						'REMEMBER_ME' => 'Remember me', 
						'NO_THANKS' => 'No, thanks!',

						'DISCOUNT_CODES' => 'Discount Codes',
						'RELATED_STROES' => 'Related Stores',
						'OTHER_RELATED_COUPONS' => 'Other Related Coupons',

						'SITE_WIDE'=>'Site Wide',
						'SALES_CLEARANCE'=>'Clearance',
						'FREE_DOWNLOAD'=>'Free Download',
						'FREE_TRIAL'=>'Free Trial',
						'REBATE'=>'Rebate',
						'REWARD'=>'Reward',

						'SITE_WIDE_OFFER'=>'Site Wide Offer',
						'SITE_WIDE_DEAL'=>'Site Wide Deal',
						'SITE_WIDE_DISCOUNT'=>'Site Wide Discount',
						'SITE_WIDE_SAVINGS'=>'Site Wide Savings',

						'SPECIAL_OFFER'=>'Special Offer',
						'SPECIAL_DEAL'=>'Special Deal',
						'SPECIAL_DISCOUNT'=>'Special Discount',
						'SPECIAL_SAVINGS'=>'Special Savings',
						"SUBMIT_STORE" => "Store Name"
				),
				'de'=>array(
						'LEFT_HOLIDAY_TITLE'=>'Urlaubsausverkauf',
						'LEFT_RELATED_STORES'=>'Bezogene Shops',
						'LEFT_RELATED_CATEGORIES'=>'Bezogene Kategorien',
						'LEFT_SEND_MAIL_MSG_1'=>'Gutscheine an dein Postfach',
						
						
						'LEFT_NEWSLETTER_PLACEHOLDER'=>'E-Mail eintragen',
						'LEFT_NEWSLETTER_BUTTON'=>'Anmelden',
						'LEFT_NEWSLETTER_MSG_1'=>'Kostenlosen DiscountsStory Newsletter erhalten!',
						
						
						'LEFT_SUBMIT_COUPON_TITLE'=>'Gutschein übermitteln',
						'LEFT_SUBMIT_COUPON_FORM_1'=>'Unbekanntes Ablaufdatum',
						'LEFT_SUBMIT_COUPON_FORM_2'=>'Nicht lesbar? Hier Text ändern.',
						'LEFT_SUBMIT_COUPON_MSG_1'=>'<strong>Danke!</strong><br><span>Dein Gutschein wurde erfolgreich übermittelt und wird zur Freigabe überprüft.</span>',
						
						'ALERT_VOTE_MSG_1'=>'Erhalte die neusten von Hand ausgewählten Promo Codes & Deals per E-Mail!',
						'ALERT_VOTE_MSG_2'=>'E-Mail eintragen',
						'ALERT_VOTE_MSG_3'=>'Bitte teilen Sie anderen Kunden mit ob dieser Rabatt geklappt hat!',
						'ALERT_VOTE_MSG_4'=>'Kopiere den Gutscheincode und trage ihn am Ende des Bestellprozess ein.',
						'ALERT_VOTE_MSG_5'=>'Code/Deal holen',
						'ALERT_VOTE_MSG_6'=>'No Coupon Code Needed',
						'ALERT_VOTE_MSG_7'=>'Ihr Rabatt wird an der Kasse automatisch eingelöst!',
						'ALERT_VOTE_MSG_8'=>'Kopiere den Gutscheincode und trage ihn am Ende des Bestellprozess ein.',
						
						'LIST_TITLE_1'=>'Hier für Gutscheincode klicken und Shop besuchen',
						'LIST_TITLE_2'=>'Was andere User auch interessiert',
						'LIST_TITLE_3'=>'Entschuldigung, es sind im Moment keine Promo Codes für <{$TermName}> verfügbar.',
						'LIST_TITLE_4'=>'Sie können unsere besten Gutscheine ausprobieren und weiter shoppen.',
						'LIST_TITLE_5'=>'Nichts gefunden? Dann probiere unsere internationalen Shops',
						'LIST_TITLE_6'=>'Wir sehen <br />dass du aus <br /><{$item.country_name}>.',
						'LIST_TITLE_7'=>'Gutscheine die vielleicht funktionieren',

						'TOPIC_TITLE_1'=>'Empfohlene Gutscheine',

						'HEADER_LOGO_TITLE'=>'Gutscheine von <{$year}>',
						
						'GET_CODE'=>'Code holen',
						'ADD_TO_FAVORITE'=>'Zu Favoriten hinzu fügen',
						'SEARCH'=>'suchen',
						'SEARCH_PLACEHOLDER'=>'Shop-Name einfügen um Promo Code zu finden',
						'PROMO'=>'Promo',
						'CODES'=>'Codes',
						'CLICKS'=>'Klicks',
						'SHARE'=>'Teilen',
						'COUPON'=>'Gutschein',
						'PROMO_CODE'=>' Gutschein',
						'PROMO_CODE_L'=>'gutschein',
						'PROMO_CODES'=>'Gutschein',
						'PROMO_DETAIL'=>'Promo Detail',
						'PROMOTION_CODE'=>'Aktionscode',
						'PROMOTIONAL_CODE'=>'Aktionscode',
						'COUPON_CODE'=>'Gutscheincode',
						'COUPON_CODES'=>'Gutscheincode',
						'COUPON_CODES_L'=>'Gutscheincode',
						'DEAL'=>'Deal',
						'DEALS'=>'Deals',
						'PROMOTION'=>'Sonderaktion',
						'OFFER'=>'Angebot',
						'OFF'=>'günstiger',
						'BUY_MORE_PAY_LESS'=>'Kaufe mehr, zahlen weniger',
						'BOGO'=>'Bogo',
						'BNGN'=>'BNGN',
						'FREE_GIFTS'=>'Kostenlose Geschenke',
						'FREE_SAMPLE'=>'Kostenlose Muster',
						'FREE_SHIPPING'=>'Kostenfreie Lieferung',
						'FREE_DELIVERY'=>'Free Delivery',
						'SPECIAL'=>'Spezial',
						'EXPIRATION_DATE'=>'Ablaufdatum',
						'EXPIRE_DATA'=>'Ablaufdatum',
						'SEE_ALL'=>'Alles anzeigen',
						'SEE_MORE'=>'Zeige mehr',
						'VOTE'=>'abstimmen',
						'SUBSCRIBE'=>'Registrieren',
						'SUBMIT'=>'Übermitteln',
						'VISIT'=>'Besuchen',
						'MORE'=>'mehr',
						'ABOUT'=>'über',
						'DISCOUNT'=>'Discount',
						'SAVINGS'=>'Savings',
						'LOGIN_WITH' => 'Einloggen mit',
						'MY_FAVORITES' => 'Meine Favoriten',  
						'FIND_YOUR_FAV_COUPONS' => 'Finde Deinen Lieblings-Coupon',
						'REMEMBER_ME' => 'Eingeloggt bleiben',
						'NO_THANKS' => 'Nein Danke!', 

						'DISCOUNT_CODES' => 'Discount Codes',
						'RELATED_STROES' => 'Ähnliche Shops',
						'OTHER_RELATED_COUPONS' => 'Ähnliche Gutscheine',

						'SITE_WIDE'=>'Site Wide',
						'SALES_CLEARANCE'=>'Schlussverkauf',
						'FREE_DOWNLOAD'=>'Freier Download',
						'FREE_TRIAL'=>'Freie Versuche',
						'REBATE'=>'Rabatt',
						'REWARD'=>'Belohnung',

						'SITE_WIDE_OFFER'=>'Angebot ALLES',
						'SITE_WIDE_DEAL'=>'Deal ALLES',
						'SITE_WIDE_DISCOUNT'=>'Rabatt ALLES',
						'SITE_WIDE_SAVINGS'=>'Ersparnisse ALLES',

						'SPECIAL_OFFER'=>'Angebot Spezial',
						'SPECIAL_DEAL'=>'Deal Spezial',
						'SPECIAL_DISCOUNT'=>'Rabatt Spezial',
						'SPECIAL_SAVINGS'=>'Ersparnisse Spezial',
						"SUBMIT_STORE" => "Shop Name"

				),
				'fr'=>array(
						'LEFT_HOLIDAY_TITLE'=>'Promo Jour de fête',
						'LEFT_RELATED_STORES'=>'Hot Stores',
						'LEFT_RELATED_CATEGORIES'=>'Catégories connexes',
						'LEFT_SEND_MAIL_MSG_1'=>'Envoyer des codes promo à votre email',
						
						
						'LEFT_NEWSLETTER_PLACEHOLDER'=>'Saisissez votre e-mail',
						'LEFT_NEWSLETTER_BUTTON'=>'Se connecter',
						'LEFT_NEWSLETTER_MSG_1'=>'Recevoir gratuitement les Newsletters DiscountsStory!',
						
						
						'LEFT_SUBMIT_COUPON_TITLE'=>'Soumettre un coupon',
						'LEFT_SUBMIT_COUPON_FORM_1'=>'Expiration Inconnue',
						'LEFT_SUBMIT_COUPON_FORM_2'=>'Illisible? Changer le texte.',
						'LEFT_SUBMIT_COUPON_MSG_1'=>'<strong>Merci!</strong><br><span>Votre coupon a été soumis avec succès et sera examiné pour approbation.</span>',
						
						'ALERT_VOTE_MSG_1'=>'Recevez les derniers codes promo & bons plans sélectionnés par e-mail !',
						'ALERT_VOTE_MSG_2'=>'Entrez votre adresse e-mail',
						'ALERT_VOTE_MSG_3'=>'Si cette réduction a fonctionné, partagez-la avec d\'autres clients , s\'il vous plaît!',
						'ALERT_VOTE_MSG_4'=>'Copiez et entrez le code promo au moment du paiement!',
						'ALERT_VOTE_MSG_5'=>'Obtenir le code /le bon plan',
						'ALERT_VOTE_MSG_6'=>'No Coupon Code Needed',
						'ALERT_VOTE_MSG_7'=>'Votre réduction sera appliqué automatiquement au moment du paiement!',
						'ALERT_VOTE_MSG_8'=>'Copiez et entrez le code promo au moment du paiement!',
						
						'LIST_TITLE_1'=>'Cliquez ici pour obtenir le code et visiter le magasin',
						'LIST_TITLE_2'=>'Autres clients s\'interressent aussi à',
						'LIST_TITLE_3'=>'Désolé, il n\'y a pas de <{$TermName}> codes promo disponibles en ce moment.',
						'LIST_TITLE_4'=>'Vous pouvez consulter nos meilleurs coupons et continuer votre shopping.',
						'LIST_TITLE_5'=>'Rien est trouvé sur votre recherche? Consultez ces magasins internationaux, s\'il vous plaît',
						'LIST_TITLE_6'=>'nous sommes en train de détecter <br />d\'où venez-vous <br /><{$item.country_name}>.',
						'LIST_TITLE_7'=>'Coupons probablement valides',

						'TOPIC_TITLE_1'=>'Codes promo recommandés',

						'HEADER_LOGO_TITLE'=>'Codes Promo de <{$year}>',
						
						'GET_CODE'=>'Obtenir le code',
						'ADD_TO_FAVORITE'=>'Ajouter au favori',
						'SEARCH'=>'rechercher',
						'SEARCH_PLACEHOLDER'=>'Entrer le nom d’un magasin pour trouver les codes promo',
						'PROMO'=>'Promo',
						'CODES'=>'Codes',
						'CLICKS'=>'clics',
						'SHARE'=>'Partager',
						'COUPON'=>'Coupon',
						'PROMO_CODE'=>'Code Promo',
						'PROMO_CODE_L'=>'code promo',
						'PROMO_CODES'=>'Codes Promo',
						'PROMO_DETAIL'=>'Détail Promo',
						'PROMOTION_CODE'=>'Bon de réduction',
						'PROMOTIONAL_CODE'=>'coupon réduction',
						'COUPON_CODE'=>'Code Réduction',
						'COUPON_CODES'=>'Codes Réduction',
						'COUPON_CODES_L'=>'codes réduction',
						'DEAL'=>'Bon plan',
						'DEALS'=>'Bons plans',
						'PROMOTION'=>'Promotion',
						'OFFER'=>'Offre',
						'OFF'=>'de Réduction',
						'BUY_MORE_PAY_LESS'=>'Acheter plus pour payer moins',
						'BOGO'=>'Bogo',
						'BNGN'=>'BNGN',
						'FREE_GIFTS'=>'Cadeaux gratuits',
						'FREE_SAMPLE'=>'Echantillon gratuit',
						'FREE_SHIPPING'=>'Livraison gratuite',
						'FREE_DELIVERY'=>'Livraison offerte',
						'SPECIAL'=>'Spécial',
						'EXPIRATION_DATE'=>'Expiration',
						'EXPIRE_DATA'=>'Date Expirée',
						'SEE_ALL'=>'Voir tout',
						'SEE_MORE'=>'Voir d\'Autre',
						'VOTE'=>'Voter',
						'SUBSCRIBE'=>'Souscrire',
						'SUBMIT'=>'Soumettre',
						'VISIT'=>'Visiter',
						'MORE'=>'d\'Autre',
						'ABOUT'=>'About',
						'DISCOUNT'=>'Discompte',
						'SAVINGS'=>'Economies',
						'LOGIN_WITH' => 'Connectez avec ', 
						'MY_FAVORITES' => 'Mes favoris', 
						'FIND_YOUR_FAV_COUPONS' => 'Trouver vos coupons de prédilection', 
						'REMEMBER_ME' => 'Se souvenir de moi', 
						'NO_THANKS' => 'Non, merci!',

						'DISCOUNT_CODES' => 'Discount Codes',
						'RELATED_STROES' => 'Magasins Connexes',
						'OTHER_RELATED_COUPONS' => 'Coupons Connexes',

						'SITE_WIDE'=>'sur tout le site',
						'SALES_CLEARANCE'=>'Déstockage',
						'FREE_DOWNLOAD'=>'Télécharger gratuitement',
						'FREE_TRIAL'=>'Tester gratuitement',
						'REBATE'=>'Remboursement',
						'REWARD'=>'Récompense',

						'SITE_WIDE_OFFER'=>'Offre sur tout le site',
						'SITE_WIDE_DEAL'=>'Bon plan sur tout le site',
						'SITE_WIDE_DISCOUNT'=>'Discompte sur tout le site',
						'SITE_WIDE_SAVINGS'=>'Economies sur tout le site',

						'SPECIAL_OFFER'=>'Offre spécial',
						'SPECIAL_DEAL'=>'Bon plan spécial',
						'SPECIAL_DISCOUNT'=>'Discompte spécial',
						'SPECIAL_SAVINGS'=>'Economies spécial',
						"SUBMIT_STORE" => "Nom de Magasin"
				),
		);
	}
	
	function get_word_dict(){
		return $this->lib[$this->lang];
	}
	
	function setLang($lang = ''){
		if($lang == 'de'){
			$this->lang = 'de';
		}
		if($lang == 'fr'){
			$this->lang = 'fr';
		}
	}
	
	function getLang(){
		return $this->lang;
	}

	function getLangTablePK($lang='en'){
		$map = $this->lang_map;
		if(isset($map[$lang])){
			return $map[$lang];
		}else{
			return false;	
		}
	}

	function getLangTableVAL($pk){
		$map = array_flip($this->lang_map);
		if(isset($map[$pk])){
			return $map[$pk];
		}else{
			return $this->getLang();	
		}
	}

	function getMonthWord($lang,$month){
		$dict = array();
		$dict['en']['January'] = 'January';
		$dict['en']['February'] = 'February';
		$dict['en']['March'] = 'March';
		$dict['en']['April'] = 'April';
		$dict['en']['May'] = 'May';
		$dict['en']['June'] = 'June';
		$dict['en']['July'] = 'July';
		$dict['en']['August'] = 'August';
		$dict['en']['September'] = 'September';
		$dict['en']['October'] = 'October';
		$dict['en']['November'] = 'November';
		$dict['en']['December'] = 'December';

		$dict['de']['January'] = 'Januar';
		$dict['de']['February'] = 'Februar';
		$dict['de']['March'] = 'März';
		$dict['de']['April'] = 'April';
		$dict['de']['May'] = 'Mai';
		$dict['de']['June'] = 'Juni';
		$dict['de']['July'] = 'Juli';
		$dict['de']['August'] = 'August';
		$dict['de']['September'] = 'September';
		$dict['de']['October'] = 'Oktober';
		$dict['de']['November'] = 'November';
		$dict['de']['December'] = 'Dezember';

		$dict['fr']['January'] = 'janvier';
		$dict['fr']['February'] = 'février';
		$dict['fr']['March'] = 'mars';
		$dict['fr']['April'] = 'avril';
		$dict['fr']['May'] = 'mai';
		$dict['fr']['June'] = 'juin';
		$dict['fr']['July'] = 'juillet';
		$dict['fr']['August'] = 'août';
		$dict['fr']['September'] = 'septembre';
		$dict['fr']['October'] = 'octobre';
		$dict['fr']['November'] = 'novembre';
		$dict['fr']['December'] = 'décembre';

		return $dict[$lang][$month];
	}

	function getMonthWordShort($lang,$month){
		$dict = array();
		$dict['en']['Jan'] = 'Jan';
		$dict['en']['Feb'] = 'Feb';
		$dict['en']['Mar'] = 'Mar';
		$dict['en']['Apr'] = 'Apr';
		$dict['en']['May'] = 'May';
		$dict['en']['Jun'] = 'Jun';
		$dict['en']['Jul'] = 'Jul';
		$dict['en']['Aug'] = 'Aug';
		$dict['en']['Sep'] = 'Sep';
		$dict['en']['Oct'] = 'Oct';
		$dict['en']['Nov'] = 'Nov';
		$dict['en']['Dec'] = 'Dec';

		$dict['de']['Jan'] = 'Januar';
		$dict['de']['Feb'] = 'Februar';
		$dict['de']['Mar'] = 'März';
		$dict['de']['Apr'] = 'April';
		$dict['de']['May'] = 'Mai';
		$dict['de']['Jun'] = 'Juni';
		$dict['de']['Jul'] = 'Juli';
		$dict['de']['Aug'] = 'August';
		$dict['de']['Sep'] = 'September';
		$dict['de']['Oct'] = 'Oktober';
		$dict['de']['Nov'] = 'November';
		$dict['de']['Dec'] = 'Dezember';

		$dict['fr']['Jan'] = 'Jan.';
		$dict['fr']['Feb'] = 'Fév.';
		$dict['fr']['Mar'] = 'Mars';
		$dict['fr']['Apr'] = 'Avr.';
		$dict['fr']['May'] = 'Mai';
		$dict['fr']['Jun'] = 'Juin';
		$dict['fr']['Jul'] = 'Juil.';
		$dict['fr']['Aug'] = 'Août';
		$dict['fr']['Sep'] = 'Sept.';
		$dict['fr']['Oct'] = 'Oct.';
		$dict['fr']['Nov'] = 'Nov.';
		$dict['fr']['Dec'] = 'Déc.';

		return $dict[$lang][$month];
	}
}
?>