<?php
//библеотека с классом Website 
//в которой формируется основной код сайта
//основная задача: сформировать сайт, в котором все действия как Get запросы и переходы на под страницы будут чательно скрыты
class Website{
protected $AllowWithOutSSL = 0;//можно ли использовать сайт без SSL
protected $SSLRedirect = "https://127.0.0.1"; //редирект на SSL
protected $SiteURL = "https://127.0.0.1";	//место,где сайт
protected $SiteHead = ""; //заголовок веб страницы
protected $SiteHat = "Проект по базам данных"; //шапка сайта
protected $SiteBoots = "Выполнил Свешников Владимир <br>DarkSvesh "; //башмаки
protected $SiteMainPageHat = "СУБД: Электронные копии документов";
protected $SiteMainPageText = "Данная система представляет собой аналог <br>СУБД \"Электронные документы\"";
protected $SiteInternalPageHat = "Внутренняя страница";
private $Username = ""; //юзер
private $Password = ""; //пароль
private $Action = ""; //действие
private $ActionString = ""; //дополнительный параметр действия
private $Page = ""; //навигация
//для поиска
private $SearchCurrentElement = 1;
private $SearchMaxElement = 0;
//лист
private $ListId = 0;
private $ListPath = "";
private $ListNum = 0;
private $ListName = "";
//дело
private $DealNum = 0;
private $DealName = "";
private $DealId = 0;
private $DealAnnot = "";
//опись
private $RegisterNum = 0;
private $RegisterName = "";
private $RegisterId = 0;
private $RegisterAnnot = "";
//фонд
private $FoundId = 0;
private $FoundNum = 0;
private $FoundName = "";
private $FoundType = 0;
private $FoundCathegory = 0;
//тегострока для поиска
private $TagLine = "";
//подключение к бд
private $RootUsername = "root";
private $RootPswd = "";
private $Database = "mydb";
private $Host = "127.0.0.1:3306";
//проверка логин-пароля
public function Login($Username, $Password){
	//запилимся к бд
	$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
	//получим хеш
	$HashU = sha1($this->Encode($Username), false);
	$HashP = sha1($this->Encode($Password), false);
	mysql_select_db($this->Database);
	$query = "SELECT * FROM users WHERE (Username = \"$HashU\") AND (Password = \"$HashP\")";
	$result = mysql_query($query);
	$AccessLevel = 0;//значение равно 0, если результат равен: Guest
	$row = mysql_fetch_assoc($result);
	$AccessLevel = mysql_affected_rows();
	mysql_free_result($result);
	mysql_close($Connection);
	return $AccessLevel;
}
//смена пароля
public function SetPassword($Username, $Password, $NewPassword){
	if($this->Login($Username,$Password)>0){
		$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
		$HashU = sha1($this->Encode($Username), false);
		$HashP = sha1($this->Encode($NewPassword), false);
		mysql_select_db($this->Database);
		$query = "UPDATE users SET Password = \"$HashP\" WHERE Username = \"$HashU\"";
		$result = mysql_query($query);
		//mysql_free_result($result);
		mysql_close($Connection);
	}
	return;
}
//Html заголовок и прочая лабуда
private function FormHeader($Title){
//стандартный хидер Html5
	Return "<!DOCTYPE html>
<html lang=ru>
	<head>
		<meta charset=utf-8>
		<title>$Title</title>
		<!--[if IE]>
        <script>
            document.createElement('header');
            document.createElement('nav');
            document.createElement('section');
            document.createElement('article');
            document.createElement('aside');
            document.createElement('footer');
        </script>
    <![endif]-->
	<link rel=\"stylesheet\" type=\"text/css\" href=\"Style.css\">
</head>
<body style=\"background-image:url(background.jpg);background-size: cover;\">";
}
//навигационные ссылки
private function FormNavigationLinks(){
	return "
<div id=\"navlinks\">
	<form id=\"NavForm\" action=\"$this->SiteURL\" method=\"POST\" >
	<div id=\"NavMain\"> 
	<button class=\"NavButton1\" form=\"NavForm\" type=\"submit\" name=\"Page\" value=\"Main\">Главная </button>
	</div>
	<div id=\"NavView\">
	<button class=\"NavButton1\" form=\"NavForm\" type=\"submit\" name=\"Page\" value=\"View\">Просмотр</button>
	</div>
	<div id=\"NavCatalog\">
	<button class=\"NavButton1\" form=\"NavForm\" type=\"submit\" name=\"Page\" value=\"Catalog\">Каталог</button>
	</div>
	<div id=\"NavEdit\">
	<button class=\"NavButton2\" form=\"NavForm\" type=\"submit\" name=\"Page\" value=\"Edit\">Редактор</button>	
	</div>
	<div id=\"NavProfile\">
	<button class=\"NavButton2\" form=\"NavForm\" type=\"submit\" name=\"Page\" value=\"Profile\">Профиль</button>	
	</div>
	<div id=\"NavInternal\">
	<button class=\"NavButton2\" form=\"NavForm\" type=\"submit\" name=\"Page\" value=\"Internal\">Внутренняя</button>	
	</div>
	</form>
</div>
	";
}
//Футер для Html заголовка
private function FormFooter(){
//усредненно: футер для html
	Return "
	</body>
</html>";
return;
}
//а еще пользователям IE я желаю сдохнуть от рака
//Тело сайта
private function FormBody(){
	//основной код сайта
	$Hat = $this->FormHat($this->SiteHat);
	$Left = $this->FormSidePanel($this->FormBlock("NavigationBlock",$this->FormNavigationBlock("Навигация:",$this->FormNavigationLinks())));
	$Right = $this->FormSidePanel($this->FormBlock("LoginBlock",$this->FormLoginBlock($this->Username)));
	$Center = $this->FormBlock("MainContentBlock",$this->FormMainContentSection($this->FormPage()));
	//Еще немного ультимативногоговнокода
	$Jacket = (($this->AllowWithOutSSL == 0) && ($this->CheckSSL() == 0))? $this->FormNoSSLError():$this->FormJacket($Left,$Center,$Right);
	$Boots = $this->FormBoots($this->SiteBoots,date('Y'));
	$Costume = $this->FormCostume($Hat,$Jacket,$Boots);
	return $Costume;
}
//Формируем код страниц
private function FormMainPage(){
	return $this->FormMainContentBlock("MainContent",$this->SiteMainPageHat,$this->SiteMainPageText);
}
private function FormViewPage(){
	return $this->FormSearchForm();
}
private function FormInternalPage(){
	return $this->FormMainContentBlock("MainContent",$this->SiteInternalPageHat,$this->SiteInternalPageText);
}
private function FormProfilePage(){
	return "
	<div id=\"UserProfile\">
		<div id=\"UserProfileCaption\">
			<label class=\"TextLabelNoEditBox1\">Профиль пользователя: $this->Username</label>
		</div>
		<div id=\"UserPasswordForm\">
			<label class=\"TextLabelNoEditBox1\">Сменить пароль: </label>
			<form action=\"$this->SiteURL\" method=\"post\">
			<div id=\"UserPassword\">
				<label class=\"TextLabel1\"> Старый:</label>
				<input class=\"TextInput1\" type=\"password\" size=\"15\"  name=\"Password\">
			</div>
			<div id=\"UserNewPassword\">
				<label class=\"TextLabel1\" > Новый:</label>
				<input Class=\"TextInput1\" type=\"password\" size=\"15\"  name=\"NewPassword\">
			</div>
			<div id=\"UserNewPasswordConfirm\">
				<label class=\"TextLabel1\"> Новый:</label>
				<input Class=\"TextInput1\" type=\"password\" size=\"15\"  name=\"PasswordConfirm\">				
			</div>
				<input Class=\"InterfaceButton1\" type=\"submit\" size=\"15\"  name=\"Action\" value=\"Сменить пароль\">
			</form>
		</div>
	</div>";
}
private function FormCatalogPage(){
	$this->FoundCathegory = (isset($_POST["FoundCathegory"]))?$_POST["FoundCathegory"]:"0";
	$this->Action = (isset($_POST["Action"]))?$_POST["Action"]:"";
	$OutBlock = "<ul>";
	if($this->Action == "Все"){
		$SQLQuery = "SELECT * FROM Cathegories ORDER BY id";
	}else{
		$SQLQuery = "SELECT * FROM Cathegories WHERE (id = ".intval($this->FoundCathegory).") ORDER BY id";
	}
	$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
	mysql_select_db($this->Database,$Connection);	
	$result = mysql_query($SQLQuery);
	while($row = mysql_fetch_array($result, MYSQL_NUM)){
		$CathegoryCaption = "
	<li>		
		<form id=\"CatalogSearchCat".$row[0]."\"	action=\"$this->SiteURL\" method=\"POST\">
			<input type=\"Hidden\" name=\"Page\" value=\"View\">
			<input type=\"Hidden\" name=\"FoundCathegory\" value=\"".$row[0]."\">
			<input type=\"Hidden\" name=\"Action\" value=\"Найти\">
			<input type=\"Hidden\" name=\"Catalog\" value=\"1\">
		</form>
		<div class=\"CathegoryCaption\">
			Категория: ".$this->Decode($row[1])." 
			<br>
			Фонд №:
		<div>
		<ul>";
		$OutBlock .= $CathegoryCaption;
		$FoundQuery = "SELECT * FROM Founds WHERE (Cathegory = ".$row[0].") ORDER BY Num";
		$FoundResult = mysql_query($FoundQuery);
		while($FoundRow = mysql_fetch_array($FoundResult, MYSQL_NUM)){
			$FoundBlock = "
			<li>	
				<form id=\"CatalogSearchFound".$FoundRow[0]."\"	action=\"$this->SiteURL\" method=\"POST\">
					<input type=\"Hidden\" name=\"Page\" value=\"View\">
					<input type=\"Hidden\" name=\"FoundNum\" value=\"".$FoundRow[1]."\">
					<input type=\"Hidden\" name=\"FoundType\" value=\"".$FoundRow[3]."\">
					<input type=\"Hidden\" name=\"Action\" value=\"Найти\">
					<input type=\"Hidden\" name=\"Catalog\" value=\"1\">
				</form>
				<div class=\"FoundCaption\">
					<a href=\"#\" onclick=\"document.forms['CatalogSearchFound".$FoundRow[0]."'].submit();\">".$FoundRow[1]." - ".$this->Decode($FoundRow[2])."</a>
					<br>
					Опись № :
				</div>
			<ul>
			";
			$RegisterQuery = "SELECT * FROM Registers WHERE (Found = ".$FoundRow[0].") ORDER BY Num";
			$RegisterResult = mysql_query($RegisterQuery);
			while($RegisterRow = mysql_fetch_array($RegisterResult, MYSQL_NUM)){
				$RegisterBlock = "
			<li>
				<form id=\"CatalogSearchRegister".$RegisterRow[0]."\"	action=\"$this->SiteURL\" method=\"POST\">
					<input type=\"Hidden\" name=\"Page\" value=\"View\">
					<input type=\"Hidden\" name=\"Action\" value=\"Найти\">
					<input type=\"Hidden\" name=\"RegisterNum\" value=\"".$RegisterRow[1]."\">
					<input type=\"Hidden\" name=\"FoundNum\" value=\"".$FoundRow[1]."\">
					<input type=\"Hidden\" name=\"FoundType\" value=\"".$FoundRow[3]."\">
					<input type=\"Hidden\" name=\"Catalog\" value=\"1\">
				</form>
				<div class=\"RegisterCaption\">
					<a href=\"#\" onclick=\"document.forms['CatalogSearchRegister".$RegisterRow[0]."'].submit();\">".$RegisterRow[1]." - ".$this->Decode($RegisterRow[3])."</a>
				</div>
			<ul>";
				$DealQuery = "SELECT * FROM Deals WHERE (Register = ".$RegisterRow[0].") ORDER BY Num";
				$DealResult = mysql_query($DealQuery);
				while($DealRow = mysql_fetch_array($DealResult, MYSQL_NUM)){
					$ListQuery = "SELECT * FROM Lists WHERE (Deal = ".$DealRow[0].") ORDER BY Num";
					$ListResult = mysql_query($ListQuery);
					$ListCount = mysql_affected_rows();
					$DealBlock = "
				<li>
					<form id=\"CatalogSearchDeal".$DealRow[0]."\"	action=\"$this->SiteURL\" method=\"POST\">
					<input type=\"Hidden\" name=\"Page\" value=\"View\">
					<input type=\"Hidden\" name=\"RegisterNum\" value=\"".$RegisterRow[1]."\">
					<input type=\"Hidden\" name=\"FoundNum\" value=\"".$FoundRow[1]."\">
					<input type=\"Hidden\" name=\"FoundType\" value=\"".$FoundRow[3]."\">
					<input type=\"Hidden\" name=\"DealNum\" value=\"".$DealRow[1]."\">
					<input type=\"Hidden\" name=\"Action\" value=\"Найти\">
					<input type=\"Hidden\" name=\"Catalog\" value=\"1\">
				</form>
					<div class=\"DealCaption\">
						<a href=\"#\" onclick=\"document.forms['CatalogSearchDeal".$DealRow[0]."'].submit();\">".$DealRow[1]." - ".$this->Decode($DealRow[2])."</a>
						<br> Листов: $ListCount
					</div>
				</li>";
					$RegisterBlock.=$DealBlock;
				}
				$FoundBlock .= $RegisterBlock;
				$FoundBlock .= "</ul></li>";
			}
			$OutBlock .= $FoundBlock;
			$OutBlock .="</ul></li>";
		}
		$OutBlock .= "</ul></li>";
	}
	mysql_free_result($result);
	mysql_close($Connection);
	$OutBlock .= "</ul>";
	$Block = "
	<div id=\"CatalogPage\">
		<div id=\"CatalogPageCaption\">
			Фондовый каталог
		</div>
		<div style=\"text-align:left\" id=\"CatalogBlock\">
		<div id=\"CatalogPanel\">
			<form id=\"CatalogSearch\"	action=\"$this->SiteURL\" method=\"POST\">
			<input form=\"CatalogSearch\" type=\"Hidden\" name=\"Page\" value=\"Catalog\">
			<center>
			<select class=\"TextInputNoLabel2\" name=\"FoundCathegory\" form=\"CatalogSearch\">
			".$this->FormSearchCathegories("")."
			</select>
			<input class=\"InterfaceButton5\" form=\"CatalogSearch\" 
			type=\"submit\" name=\"Action\" value=\"Найти\">
			<input class=\"InterfaceButton5\" form=\"CatalogSearch\" 
			type=\"submit\" name=\"Action\" value=\"Все\">
			</center>
			</form>
		</div>
			<center>Список категорий:</center>
				$OutBlock 
		</div>
	</div>
	";
	return $Block;
}
//вывод категорий фондов для поиска
private function FormSearchCathegories($RowId){
	$SQLQuery = "SELECT * FROM cathegories";
	$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
	mysql_select_db($this->Database,$Connection);	
	$result = mysql_query($SQLQuery	);
	$Block = "<option value=\"0\"></option>";
	$Selected = "";
	while($row = mysql_fetch_array($result, MYSQL_NUM)){
		if(intval($RowId) == intval($row[0])){$Selected = "Selected";}else{$Selected="";}
		$Result = "<option value=\"".$row[0]."\" $Selected>".$this->Decode($row[1])."</option>";
		$Block .= $Result;
	}
	mysql_free_result($result);
	mysql_close($Connection);
	return $Block;
}
//фрейм поиска
private function FormSearchFrame(){
	return"				
<center>
	<form method=\"post\" action=\"$this->SiteURL\">
		<table style=\"text-align:center\">
			<tr>
				<td>
					<div class=\"TableCaption1\">
						Фонд
					</div>
				</td>
				<td>
					<div class=\"TableCaption1\">
						Тип фонда
					</div>
				</td>
				<td>
					<div class=\"TableCaption1\">
						Опись
					</div>
				</td>
				<td>
					<div class=\"TableCaption1\">
						Дело
					</div>
				</td>
				<td>
					<div class=\"TableCaption1\">
						Лист
					</div>
				</td>
				<td>
					<div class=\"TableCaption1\">
						Действие
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class=\"TextLabel2\">
						Номер
					</div>
					<input class=\"TextInput2\" type=\"number\" size=\"5\" name=\"FoundNum\">
					<br>	
					<div class=\"TextLabel2\">
						Имя
					</div>
					<input style=\"margin-top:1px;\" class=\"TextInput2\" type=\"text\" size=\"5\" name=\"FoundName\">
				</td>
				<td>
					<div class=\"TextLabel3\">
						Тип
					</div>
					<label class=\"TextInput3\">
						<input type=\"checkbox\" name=\"FoundType\" checked value=\"1\">
						Совр.
					</label>
					<br>
					<div class=\"TextLabel4\">
						Кат-я
					</div>
					<select class=\"TextInput4\" name=\"FoundCathegory\">
						".$this->FormSearchCathegories(0)."
					</select>
				</td>
				<td>
					<div class=\"TextLabel2\">
						Номер
					</div>
					<input class=\"TextInput2\" type=\"number\" size=\"5\" name=\"RegisterNum\">
				<br>
					<div class=\"TextLabel2\">
						Имя
					</div>
					<input class=\"TextInput2\" type=\"text\" size=\"5\" name=\"RegisterName\">
				</td>
				<td>
				<div class=\"TextLabel2\">
					Номер
				</div>
				<input class=\"TextInput2\" type=\"number\" size=\"5\" name=\"DealNum\">
				<br>
				<div class=\"TextLabel2\">
					Имя
				</div>
					<input class=\"TextInput2\" type=\"text\" size=\"5\" name=\"DealName\">
				</td>
				<td>
				<div class=\"TextLabel2\">
					Номер
				</div>
					<input class=\"TextInput2\" type=\"number\" size=\"5\" name=\"ListNum\">
				<br>
				<div class=\"TextLabel2\">
					Имя
				</div>
					<input class=\"TextInput2\" type=\"text\" size=\"5\" name=\"ListName\">
				</td>
				<td>
					<input class=\"InterfaceButton2\" type=\"submit\" name=\"Action\" value=\"Найти\">
				</td>
			</tr>
		</table>
		<input type=\"hidden\" name=\"Page\" value=\"View\">
	</form>
</center>
	";
}
//Панель поиска
private function FormSearchPanel(){
	return"				
				<form method=\"post\" action=\"$this->SiteURL\">
					<input class=\"SlideButton1\" type=\"submit\" name=\"Slide\" value=\"<<=\">
					<input class=\"SlideButton2\" type=\"submit\" name=\"Slide\" value=\"<-\">
					<input class=\"SlideMarker\" type=\"number\" size=\"2\" name=\"SearchCurrentElement2\" value=\"$this->SearchCurrentElement\" disabled>
					<input class=\"SlideMarker\" type=\"hidden\" size=\"2\" name=\"SearchCurrentElement\" value=\"$this->SearchCurrentElement\" >
					<input class=\"SlideMarker\" type=\"number\" size=\"2\" name=\"SearchMaxElement\" value=\"$this->SearchMaxElement\" disabled>
					<input class=\"SlideButton3\" type=\"submit\" name=\"Slide\" value=\"->\">
					<input class=\"SlideButton4\" type=\"submit\" name=\"Slide\" value=\"=>>\">
					<input type=\"hidden\" name=\"Action\" value=\"Найти\">
					<input type=\"hidden\" name=\"FoundNum\" value=\"$this->FoundNum\">
					<input type=\"hidden\" name=\"RegisterNum\" value=\"$this->RegisterNum\">
					<input type=\"hidden\" name=\"DealNum\" value=\"$this->DealNum\">
					<input type=\"hidden\" name=\"ListNum\" value=\"$this->ListNum\">
					<input type=\"hidden\" name=\"ListName\" value=\"$this->ListName\">
					<input type=\"hidden\" name=\"DealName\" value=\"$this->DealName\">
					<input type=\"hidden\" name=\"RegisterName\" value=\"$this->RegisterName\">
					<input type=\"hidden\" name=\"FoundName\" value=\"$this->FoundName\">
					<input type=\"hidden\" name=\"FoundType\" value=\"$this->FoundType\">
					<input type=\"hidden\" name=\"FoundCathegory\" value=\"$this->FoundCathegory\">
					<input type=\"hidden\" name=\"TagLine\" value=\"$this->TagLine\">
					<input type=\"hidden\" name=\"Page\" value=\"View\">
				</form>
	";
}
//форма  редактора листа
private function FormListEdit(){
	$Error = "";
	$ResultBlock = "";
	$this->FoundId = (isset($_POST["FoundId"]))?$_POST["FoundId"]:"";
	$this->FoundNum = (isset($_POST["FoundNum"]))?$_POST["FoundNum"]:"";
	$this->FoundType = (isset($_POST["FoundType"]))?$_POST["FoundType"]:"0";
	$this->RegisterId = (isset($_POST["RegisterId"]))?$_POST["RegisterId"]:"";
	$this->RegisterNum = (isset($_POST["RegisterNum"]))?$_POST["RegisterNum"]:"";
	$this->DealId = (isset($_POST["DealId"]))?$_POST["DealId"]:"";
	$this->DealNum = (isset($_POST["DealNum"]))?$_POST["DealNum"]:"";
	$this->ListId = (isset($_POST["ListId"]))?$_POST["ListId"]:"";
	$this->ListNum = (isset($_POST["ListNum"]))?$_POST["ListNum"]:"";
	$this->ListName = (isset($_POST["ListName"]))?$_POST["ListName"]:"";
	$this->ListPath = (isset($_POST["ListPath"]))?$_POST["ListPath"]:"";
	$this->Action = (isset($_POST["Action"]))?$_POST["Action"]:"";
	if($this->Action !==""){
		if(($this->Action =="Добавить")||($this->Action =="Изменить")||($this->Action =="Найти")){
			if($this->FoundId==""){
				if($this->CheckFoundExistance($this->FoundNum, $this->FoundType))
					$this->FoundId = $this->FindFoundByNum($this->FoundNum, $this->FoundType);
			}
			if($this->FoundId!==""){
				if($this->RegisterId==""){
					if($this->CheckRegisterExistance($this->FoundId, $this->RegisterNum))
						$this->RegisterId = $this->FindRegisterByNum($this->FoundId, $this->RegisterNum);
				}
				if($this->RegisterId!==""){
					if($this->DealId==""){
						if($this->CheckDealExistance($this->FoundId, $this->RegisterId, $this->DealNum))
							$this->DealId = $this->FindDealByNum($this->FoundId, $this->RegisterId, $this->DealNum);
					}
					if($this->DealId!==""){
						if(($this->ListNum !== "") && ($this->ListPath !== "")){
							if($this->Action == "Добавить"){
								if($this->ListId !== ""){
									$this->Action = "Изменить";
								}else{
									$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
									mysql_select_db($this->Database,$Connection);	
									$SQLQuery = "INSERT INTO Lists (Num, Name, Found, Register, Deal, Path) values (";
									$SQLQuery.=intval($this->ListNum).",\"";
									$SQLQuery.=$this->Encode($this->ListName)."\",";
									$SQLQuery.=intval($this->FoundId).",";
									$SQLQuery.=intval($this->RegisterId).",";
									$SQLQuery.=intval($this->DealId).",\"";
									$SQLQuery.=$this->Encode($this->ListPath)."\")";
									$result = mysql_query($SQLQuery);
									mysql_close($Connection);
								}
							}
							if($this->Action == "Изменить"){
									$SQLQuery = "UPDATE Lists set Num = ";
									$SQLQuery.=intval($this->ListNum).", Name = \"";
									$SQLQuery.=$this->Encode($this->ListName)."\", Found = ";
									$SQLQuery.=intval($this->FoundId).", Register = ";
									$SQLQuery.=intval($this->RegisterId).", Deal = ";
									$SQLQuery.=intval($this->DealId).", Path = \"";
									$SQLQuery.=$this->Encode($this->ListPath)."\" ";
									$SQLQuery.="WHERE (id = ".intval($this->ListId).")";
									$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
									mysql_select_db($this->Database,$Connection);	
									$result = mysql_query($SQLQuery);
									mysql_close($Connection);									
							}
						}else $Error = "Неуказан номер или путь к файлу листа!";
					}else
						$Error = "Не указано дело!";
				}else
					$Error = "Не указана опись!";
			}else
				$Error = "Не указан фонд";
			if($this->Action == "Найти"){
					$Error = "";
					$SQLQuery = "SELECT * FROM Lists WHERE (1 = 1)";
					if($this->FoundId!=="")
						$SQLQuery .="AND(Found = ".intval($this->FoundId).")";				
					if($this->RegisterId !== "")	
						$SQLQuery .="AND(Register = ".intval($this->RegisterId).")";
					if($this->DealId!=="")
						$SQLQuery .= "AND(Deal = ".intval($this->DealId).")";
					if($this->DealAnnot!=="")
						$SQLQuery .= "AND(Num = ".intval($this->ListNum).")";
					if($this->DealName!=="")
						$SQLQuery .= "AND(Name LIKE \"%".$this->Encode($this->ListName)."%\")";
					if($this->DealName!=="")
						$SQLQuery .= "AND(Path LIKE \"%".$this->Encode($this->ListPath)."%\")";
					$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
					mysql_select_db($this->Database,$Connection);
					$RowFoundId = "";
					$RowFoundNum = "";
					$RowFoundType = "";
					$RowRegisterId = ""	;
					$RowRegisterNum = "";
					$RowDealId = "";
					$RowDealNum = "";
					$RowListNum = "";
					$RowListName = "";
					$RowListId = "";
					$RowListPath = "";
					$ResultBlock = "<table border=\"0\" style=\"font-weight:bold\" class=\"TableBlock1\">";
					$result = mysql_query($SQLQuery);
					while($row = mysql_fetch_array($result, MYSQL_NUM)){
						$RowListId = $row[0];
						$RowListNum = intval($row[1]);
						$RowListName = $this->Decode($row[2]);
						$RowListPath = $this->Decode($row[6]);
						$RowDealId = intval($row[5]);
						$RowFoundId = intval($row[3]);
						$RowRegisterId =  $row[4];
						$SQLQuery = "SELECT * FROM Founds WHERE (id = $RowFoundId)";
						$FoundSearchResult = mysql_query($SQLQuery);
						$FoundRow = mysql_fetch_array($FoundSearchResult, MYSQL_NUM);
						$RowFoundType = (intval($FoundRow[3])==0)?"":"checked";
						$RowFoundNum = intval($FoundRow[1]);
						$RowType = "";
						if($row[3] == "1")
							$RowType = "Checked";
						$SQLQuery = "SELECT * FROM Registers WHERE (id = $RowRegisterId)";
						$RegisterSearchResult = mysql_query($SQLQuery);
						$RegisterRow = mysql_fetch_array($RegisterSearchResult, MYSQL_NUM);
						$RowRegisterNum = intval($RegisterRow[1]);
						if($row[3] == "1")
							$RowType = "Checked";
						$SQLQuery = "SELECT * FROM Deals WHERE (id = $RowDealId)";
						$DealSearchResult = mysql_query($SQLQuery);
						$DealRow = mysql_fetch_array($DealSearchResult, MYSQL_NUM);
						$RowDealNum = intval($RegisterRow[1]);
						$ResultRow = "
				<tr>
					<td>
						<form action=\"$this->SiteURL\" id=\"ListEdit$RowListId\" method=\"POST\">
							<input type=\"hidden\" name=\"Page\" value=\"Lists\">
						</form>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit$RowListId\" 
						type=\"text\" name=\"FoundId\" placeholder=\"Id\" value=\"$RowFoundId\">
					</td>
					<td>
						<div class=\"TableCaption3\">Тип</div>
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit$RowListId\" 
						type=\"text\" name=\"RegisterId\" placeholder=\"Id\" value=\"$RowRegisterId\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit$RowListId\" 
						type=\"text\" name=\"DealId\" placeholder=\"Id\" value=\"$RowDealId\">					
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit$RowListId\" 
						type=\"text\" name=\"ListId\" placeholder=\"Id\" value=\"$RowListId\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit$RowListId\" 
						type=\"text\" name=\"ListNum\" placeholder=\"Номер\" value=\"$RowListNum\">
					</td>
					<td>
						<div class=\"TableCaption3\">Действие</div>
					</td>
				</tr>
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit$RowListId\" 
						type=\"text\" name=\"FoundNum\" placeholder=\"Номер\" value=\"$RowFoundNum\">
					</td>
					<td>
						<input  form=\"ListEdit$RowListId\" 
						type=\"checkbox\" value=\"1\" name=\"FoundType\" $RowType>Совр.
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit$RowListId\" 
						type=\"text\" name=\"RegisterNum\" placeholder=\"Номер\" value=\"$RowRegisterNum\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit$RowListId\" 
						type=\"text\" name=\"DealNum\" placeholder=\"Номер\" value=\"$RowDealNum\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit$RowListId\" 
						type=\"text\" name=\"ListName\" placeholder=\"Имя\" value=\"$RowListName\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit$RowListId\" 
						type=\"text\" name=\"ListPath\" placeholder=\"Путь\" value=\"$RowListPath\">	
					</td>
					<td>
						<input class=\"InterfaceButton3\" form=\"ListEdit$RowListId\" type=\"submit\" name=\"Action\" value=\"Удалить\">
						<input class=\"InterfaceButton4\" form=\"ListEdit$RowListId\" type=\"submit\" name=\"Action\" value=\"Изменить\">
					</td>
				</tr>";
						$ResultBlock.=$ResultRow;
					}
					//mysql_free_result($result);
					//mysql_close($Connection);
					$ResultBlock .= "</table>";
			}
		}
		if($this->Action == "Удалить"){
			if($this->ListId !== ""){
				$SQLQuery = "DELETE FROM Lists WHERE (id = ".intval($this->ListId).")";
				$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
				mysql_select_db($this->Database,$Connection);	
				$result = mysql_query($SQLQuery);
				mysql_close($Connection);
			}
		}
	}
	$Block = "
	<div style=\"font-weight: bold\">
		<form action=\"$this->SiteURL\" id=\"ListEdit\" method=\"POST\">
			<input type=\"hidden\" name=\"Page\" value=\"Lists\">
		</form>
		<div id=\"ErrorCaption\">
			$Error
		</div>
		<div id=\"FoundEditor\" class=\"TableBlock1\">
			<div style=\"text-align:center;\" class=\"TableCaption2\">
				Фонд
			</div>
			<table border=\"0\">
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit\" 
						type=\"text\" name=\"FoundId\" placeholder=\"Id\">
					</td>
					<td>
						<div class=\"TableCaption3\">Тип</div>
					</td>
				</tr>
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit\" 
						type=\"text\" name=\"FoundNum\" placeholder=\"Номер\">
					</td>
					<td>
						<input  form=\"ListEdit\" value=\"1\"
						type=\"checkbox\" name=\"FoundType\" checked>Совр.
					</td>
				</tr>
			</table>
		</div>
		<div id=\"RegisterEditor\" class=\"TableBlock1\">
			<div style=\"text-align:center;\" class=\"TableCaption2\">
				Опись
			</div>
			<table border=\"0\">
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit\" 
						type=\"text\" name=\"RegisterId\" placeholder=\"Id\">
					</td>
				</tr>
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit\" 
						type=\"text\" name=\"RegisterNum\" placeholder=\"Номер\">
					</td>
				</tr>
			</table>
		</div>
		<div id=\"DealEditor\" class=\"TableBlock1\">
			<div style=\"text-align:center;\" class=\"TableCaption2\">
				Дело
			</div>
			<table border=\"0\">
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit\" 
						type=\"text\" name=\"DealId\" placeholder=\"Id\">					
					</td>
				</tr>
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit\" 
						type=\"text\" name=\"DealNum\" placeholder=\"Номер\">
					</td>
				</tr>
			</table>
		</div>
		<div id=\"ListEditor\" class=\"TableBlock1\">
			<div style=\"text-align:center\" class=\"TableCaption2\">
				Лист
			</div>
			<table border=\"0\">
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit\" 
						type=\"text\" name=\"ListId\" placeholder=\"Id\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit\" 
						type=\"text\" name=\"ListNum\" placeholder=\"Номер\">
					</td>
					<td>
						<div class=\"TableCaption3\">Действие</div>
					</td>
				</tr>
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit\" 
						type=\"text\" name=\"ListName\" placeholder=\"Имя\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"ListEdit\" 
						type=\"text\" name=\"ListPath\" placeholder=\"Путь\">	
					</td>
					<td>
						<input class=\"InterfaceButton3\" form=\"ListEdit\" type=\"submit\" name=\"Action\" value=\"Найти\">
						<input class=\"InterfaceButton4\" form=\"ListEdit\" type=\"submit\" name=\"Action\" value=\"Добавить\">
					</td>
				</tr>
			</table>
		</div>
		<div id=\"SearchResult\">
			$ResultBlock
		</div>
	</div>
		";
		return $Block;
}
//Форма добавления \ удаления описи
private function FormRegisterEdit(){
	$Error = "";
	$ResultBlock = "";
	$this->FoundId = (isset($_POST["FoundId"]))?$_POST["FoundId"]:"";
	$this->FoundNum = (isset($_POST["FoundNum"]))?$_POST["FoundNum"]:"";
	$this->FoundType = (isset($_POST["FoundType"]))?$_POST["FoundType"]:"0";
	$this->RegisterId = (isset($_POST["RegisterId"]))?$_POST["RegisterId"]:"";
	$this->RegisterNum = (isset($_POST["RegisterNum"]))?$_POST["RegisterNum"]:"";
	$this->RegisterName = (isset($_POST["RegisterName"]))?$_POST["RegisterName"]:"";
	$this->RegisterAnnot = (isset($_POST["RegisterAnnot"]))?$_POST["RegisterAnnot"]:"";
	$this->Action = (isset($_POST["Action"]))?$_POST["Action"]:"";
	if($this->Action !== ""){
		if($this->Action == "Добавить"){
			if($this->RegisterId !== ""){
			//изменить
				$FoundId = "";
				if($this->FoundId !== ""){
					$FoundId = "\", Found = ".intval($this->FoundId);
				}
				if($FoundId == ""){
					if($this->FoundNum !== ""){
						if($this->CheckFoundExistance($this->FoundNum, $this->FoundType)){
							$FoundId = "\", Found = ".$this->FindFoundByNum($this->FoundNum, $thisFoundType);
						}
					}
				}
				$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
				mysql_select_db($this->Database,$Connection);	
				$SQLQuery = "UPDATE Registers SET  Num =";
				$SQLQuery .=intval($this->RegisterNum);
				$SQLQuery .=", Name = \"".$this->Encode($this->RegisterName);
				$SQLQuery .=$FoundId;
				$SQLQuery .=", Annot = \"".$this->Encode($this->RegisterAnnot)."\"";
				$SQLQuery .= " WHERE (Id = ".$this->RegisterId.")";
				$result = mysql_query($SQLQuery);
				mysql_close($Connection);
			}else{
				$FoundId = "";
				if($this->FoundId !== ""){
					$FoundId = intval($this->FoundId);
				}
				if($FoundId == "")
					if($this->FoundNum !== "")
						if($this->CheckFoundExistance($this->FoundNum, $this->FoundType))
							$FoundId =$this->FindFoundByNum($this->FoundNum, $this->FoundType);
				if($FoundId !== ""){
					if(!$this->CheckRegisterExistance($FoundId, $this->RegisterNum)){
					$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
					mysql_select_db($this->Database,$Connection);	
					$SQLQuery = "INSERT INTO Registers (Num, Found, Name, Annot) values (";
					$SQLQuery .=intval($this->RegisterNum);
					$SQLQuery .=",".intval($FoundId);
					$SQLQuery .=",\"".$this->Encode($this->RegisterName);
					$SQLQuery .="\",\"".$this->Encode($this->RegisterAnnot)."\")";
					$result = mysql_query($SQLQuery);
					mysql_close($Connection);
					}else{
						$Error = "Такая опись уже есть в данном фонде.";
					}
				}else{
					$Error = "Фонд не указан!";
				}
			}
		}
		if($this->Action == "Изменить"){
			//изменить
			if($this->RegisterId !== "0"){
			//изменить
				$FoundId = "";
				if($this->FoundId !== ""){
					$FoundId = intval($this->FoundId);
				}
				if($FoundId == ""){
					if($this->FoundNum !== ""){
						if($this->CheckFoundExistance($this->FoundNum, $this->FoundType)){
							$FoundId = $this->FindFoundByNum($this->FoundNum, $this->FoundType);
						}
					}
				}
				$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
				mysql_select_db($this->Database,$Connection);	
				$SQLQuery = "UPDATE Registers SET  Num =";
				$SQLQuery .=intval($this->RegisterNum);
				$SQLQuery .=", Name = \"%".$this->Encode($this->RegisterName);
				$SQLQuery .="%\", Found =".$FoundId;
				$SQLQuery .=", Annot = \"%".$this->Encode($this->RegisterAnnot)."%\"";
				$SQLQuery .= "	 WHERE (Id = ".$this->RegisterId.")";
				$result = mysql_query($SQLQuery);
				mysql_close($Connection);
			}else{
				$Error = "Ошибка: нет Id Описи";
			}
		}
		if($this->Action == "Удалить"){
			$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
			mysql_select_db($this->Database,$Connection);	
			$SQLQuery = "DELETE FROM Registers WHERE (Id =".intval($this->RegisterId).")";
			$result = mysql_query($SQLQuery);
			mysql_close($Connection);
		}
		if($this->Action == "Найти"){
			$SQLQuery = "SELECT * FROM registers WHERE (1 = 1)";
			if($this->RegisterNum!==""){
				$SQLQuery .= "AND(Num = ".intval($this->RegisterNum).")";
			}
			if($this->RegisterAnnot!==""){
				$SQLQuery .= "AND(Annot = \"".$this->Encode($this->RegisterAnnot)."\")";
			}
			if($this->RegisterName!==""){
				$SQLQuery .= "AND(Name = \"".$this->Encode($this->RegisterName)."\")";
			}
			if($this->RegisterId!==""){
				$SQLQuery .= "AND(Id = ".intval($this->RegisterId).")";
			}
			if($this->FoundId!==""){
				$SQLQuery .= "AND(Found = ".intval($this->FoundId).")";
			}else{
				if($this->FoundNum!==""){
					$SQLQuery .= "AND(Found = ".intval($this->FindFoundByNum($this->FoundNum, $this->FoundType)).")";
				}
			}
			$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
			mysql_select_db($this->Database,$Connection);
			$RowFoundId = "";
			$RowFoundNum = "";
			$RowFoundType = "";
			$RowRegisterAnnot = "";
			$RowRegisterName = "";
			$RowRegisterNum = "";
			$RowRegisterId = "";
			$ResultBlock = "<table border=\"0\" class=\"TableBlock1\">";
			$result = mysql_query($SQLQuery);
			while($row = mysql_fetch_array($result, MYSQL_NUM)){
				$RowRegisterId = $row[0];
				$RowRegisterNum = intval($row[1]);
				$RowFoundId = $row[2];
				$RowRegisterName = $this->Decode($row[3]);
				$RowRegisterAnnot = $this->Decode($row[4]);
				$SQLQuery = "SELECT * FROM Founds WHERE (id = $RowFoundId)";
				$FoundSearchResult = mysql_query($SQLQuery);
				$FoundRow = mysql_fetch_array($FoundSearchResult, MYSQL_NUM);
				$RowFoundType = (intval($FoundRow[3])==0)?"":"checked";
				$RowFoundNum = intval($FoundRow[1]);
				$RowType = "";
				if($row[3] == "1")
					$RowType = "Checked";
				$ResultRow = "
				<tr>
					<td>
						<form action=\"$this->SiteURL\" id=\"RegisterEdit$RowRegisterId\" method=\"POST\">
						<input type=\"hidden\" name=\"Page\" value=\"Registers\">
						</form>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit$RowRegisterId\" 
						type=\"Number\" name=\"FoundId\" value=\"$RowFoundId\" placeholder=\"Id\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit$RowRegisterId\" 
						type=\"Number\" name=\"FoundNum\" value=\"$RowFoundNum\" placeholder=\"Номер\">
					</td>
					<td>
						<input  form=\"RegisterEdit\" 
						type=\"checkbox\" value=\"1\" name=\"FoundType\" $RowFoundType>Совр.
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit$RowRegisterId\" 
						type=\"text\" name=\"RegisterId\" value=\"$RowRegisterId\" placeholder=\"Id\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit$RowRegisterId\" 
						type=\"text\" name=\"RegisterNum\" value=\"$RowRegisterNum\" placeholder=\"Номер\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit$RowRegisterId\" 
						type=\"text\" name=\"RegisterName\" value=\"$RowRegisterName\" placeholder=\"Имя\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit$RowRegisterId\" 
						type=\"text\" name=\"RegisterAnnot\" value=\"$RowRegisterAnnot\" placeholder=\"Аннотация\">	
					</td>
					<td>
						<input class=\"InterfaceButton3\" form=\"RegisterEdit$RowRegisterId\" type=\"submit\" name=\"Action\" value=\"Изменить\">
						<input class=\"InterfaceButton4\" form=\"RegisterEdit$RowRegisterId\" type=\"submit\" name=\"Action\" value=\"Удалить\">
					</td>
				</tr>
				";
				$ResultBlock.=$ResultRow;
			}
			//mysql_free_result($result);
			//mysql_close($Connection);
			$ResultBlock .= "</table>";
		}
	}
	$Block = "
		<form action=\"$this->SiteURL\" id=\"RegisterEdit\" method=\"POST\">
			<input type=\"hidden\" name=\"Page\" value=\"Registers\">
		</form>
		<div id=\"ErrorCaption\">
			$Error
		</div>
		<div id=\"FoundEditor\" style=\"font-weight:bold;\" class=\"TableBlock1\">
			<div style=\"text-align:center;\" class=\"TableCaption2\">
				Фонд
			</div>
			<table border=\"0\">
				<tr>
					<td>
						<div class=\"TableCaption3\">Id</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Номер</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Тип</div>					
					</td>
				</tr>
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit\" 
						type=\"Number\" name=\"FoundId\" placeholder=\"Id\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit\" 
						type=\"Number\" name=\"FoundNum\" placeholder=\"Номер\">
					</td>
					<td>
						<input  form=\"RegisterEdit\" 
						type=\"checkbox\" value=\"1\" name=\"FoundType\" checked>Совр.
					</td>
				</tr>
			</table>
		</div>
		<div id=\"RegisterEditor\" style=\"font-weight:bold;\" class=\"TableBlock1\">
			<div style=\"text-align:center;\" class=\"TableCaption2\">
				Опись
			</div>
			<table border=\"0\">
				<tr>
					<td>
						<div class=\"TableCaption3\">Id</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Номер</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Имя</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Аннотация</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Действие</div>
					</td>
				</tr>
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit\" 
						type=\"text\" name=\"RegisterId\" placeholder=\"Id\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit\" 
						type=\"text\" name=\"RegisterNum\" placeholder=\"Номер\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit\" 
						type=\"text\" name=\"RegisterName\" placeholder=\"Имя\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"RegisterEdit\" 
						type=\"text\" name=\"RegisterAnnot\" placeholder=\"Аннотация\">	
					</td>
					<td>
						<input class=\"InterfaceButton3\" form=\"RegisterEdit\" type=\"submit\" name=\"Action\" value=\"Найти\">
						<input class=\"InterfaceButton4\" form=\"RegisterEdit\" type=\"submit\" name=\"Action\" value=\"Добавить\">
					</td>
				</tr>
			</table>
		</div>
		<div id=\"ResultBlock\">
			$ResultBlock
		</div>
		";
	return $Block;
}
//найти дело по номеру, описи и фонду
private function FindDealByNum($FoundId, $RegisterId, $DealNum){
	$Result = 0;
	$SQLQuery = "SELECT * FROM deals WHERE ( Num = ".intval($DealNum).")";
	$SQLQuery .= "AND( Found = ".intval($FoundId).")";
	$SQLQuery .= "AND( Register = ".intval($RegisterId).")";
	$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
	mysql_select_db($this->Database,$Connection);	
	$result = mysql_query($SQLQuery);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$Result = $row[0];
	mysql_close($Connection);
	return $Result;
}
//найти опись по номеру и фонду
private function FindRegisterByNum($FoundId, $RegisterNum){
	$Result = 0;
	$SQLQuery = "SELECT * FROM registers WHERE ( Num = ".intval($RegisterNum).")";
	$SQLQuery .= "AND( Found = ".intval($FoundId).")";
	$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
	mysql_select_db($this->Database,$Connection);	
	$result = mysql_query($SQLQuery);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$Result = $row[0];
	mysql_close($Connection);
	return $Result;
}
//найти фонд по номеру и типу
private function FindFoundByNum($FoundNum, $FoundType){
	$Result = "0";
	$SQLQuery = "SELECT * FROM Founds WHERE ( Num = ".intval($FoundNum).")";
	$SQLQuery .= "AND( Type = ".intval($FoundType).")";
	$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
	mysql_select_db($this->Database,$Connection);	
	$result = mysql_query($SQLQuery);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$Result = $row[0];
	mysql_close($Connection);
	return $Result;
}
//Форма добавления \ удаления дел
private function FormDealEdit(){
	$Error = "";
	$ResultBlock = "";
	$this->FoundId = (isset($_POST["FoundId"]))?$_POST["FoundId"]:"";
	$this->FoundNum = (isset($_POST["FoundNum"]))?$_POST["FoundNum"]:"";
	$this->FoundType = (isset($_POST["FoundType"]))?$_POST["FoundType"]:"0";
	$this->RegisterId = (isset($_POST["RegisterId"]))?$_POST["RegisterId"]:"";
	$this->RegisterNum = (isset($_POST["RegisterNum"]))?$_POST["RegisterNum"]:"";
	$this->DealId = (isset($_POST["DealId"]))?$_POST["DealId"]:"";
	$this->DealNum = (isset($_POST["DealNum"]))?$_POST["DealNum"]:"";
	$this->DealName = (isset($_POST["DealName"]))?$_POST["DealName"]:"";
	$this->DealAnnot = (isset($_POST["DealAnnot"]))?$_POST["DealAnnot"]:"";
	$this->Action = (isset($_POST["Action"]))?$_POST["Action"]:"";
	if($this->Action!==""){
		if($this->FoundId == "")
			if($this->FoundNum !== "")
				if($this->CheckFoundExistance($this->FoundNum,$this->FoundType))
					$this->FoundId = $this->FindFoundByNum($this->FoundNum,$this->FoundType);
		if($this->RegisterId=="")
			if($this->FoundId!=="")
				if($this->RegisterNum !== "")
					if($this->CheckRegisterExistance($this->FoundId, $this->RegisterNum))
						$this->RegisterId = $this->FindRegisterByNum($this->FoundId, $this->RegisterNum);
		if($this->Action=="Добавить"){
			if($this->DealId !==""){
				$this->Action = "Изменить";
			}else{
				if($this->FoundId !== ""){
					if($this->RegisterId!==""){
						if($this->DealNum!==""){
							if(!$this->CheckDealExistance($this->FoundId,$this->RegisterId,$this->DealNum)){
								$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
								mysql_select_db($this->Database,$Connection);
								$SQLQuery = "INSERT INTO Deals (Num, Name, Found, Register, Annot) values(";
								$SQLQuery .= intval($this->DealNum).",\"";
								$SQLQuery .= $this->Encode($this->DealName)."\",";
								$SQLQuery .= intval($this->FoundId).",";
								$SQLQuery .= intval($this->RegisterId).",\"";
								$SQLQuery .= $this->Encode($this->DealAnnot)."\")";
								$result = mysql_query($SQLQuery);
								mysql_close($Connection);
							}else $Error = "Такое дело уже существует!";
						}else{
							$Error = "Не указан номер дела";
						}
					}else{
						$Error = "Не указана опись!";
					}
				}else{
					$Error = "Не указан Фонд!";
				}
			}
		}
		if($this->Action=="Изменить"){
			if(($this->FoundId!=="")&&($this->RegisterId!=="")&&$this->DealNum!==""){	
					$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
					mysql_select_db($this->Database,$Connection);	
					$SQLQuery = "UPDATE Deals SET  Num =";
					$SQLQuery .=intval($this->DealNum);
					$SQLQuery .=", Name = \"".$this->Encode($this->DealName);
					$SQLQuery .="\", Found = ".intval($this->FoundId);
					$SQLQuery .=", Register = ".intval($this->RegisterId);
					$SQLQuery .=", Annot = \"".$this->Encode($this->DealAnnot)."\"";
					$SQLQuery .= " WHERE (Id = ".$this->DealId.")";
					$result = mysql_query($SQLQuery);
					mysql_close($Connection);
			}
		}
		if($this->Action=="Удалить"){
			$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
			mysql_select_db($this->Database,$Connection);	
			$SQLQuery = "DELETE FROM Deals WHERE (Id =".intval($this->DealId).")";
			$result = mysql_query($SQLQuery);
			mysql_close($Connection);
		}
		if($this->Action=="Найти"){
			$FoundId = "";
			if($this->FoundId == ""){
				if($this->FoundNum !== ""){
					if($this->CheckFoundExistance($this->FoundNum, $this->FoundType))
						$FoundId = $this->FindFoundByNum($this->FoundNum, $this->FoundType);
				}
			}else $FoundId = $this->FoundId;
			if($this->RegisterId){
				if($FoundId!==""){
					if($this->RegisterNum!=="")
						if($this->CheckRegisterExistance($this->FoundId,$this->RegisterNum))
							$RegisterId = $this->FindRegisterByNum($this->FoundId,$this->RegisterNum);
				}
			}else $RegisterId = $this->RegisterId;
			$SQLQuery = "SELECT * FROM Deals WHERE (1 = 1)";
			if($FoundId!=="")
				$SQLQuery .="AND(Found = ".intval($FoundId).")";				
			if($RegisterId !== "")	
				$SQLQuery .="AND(Register = ".intval($RegisterId).")";
			if($this->DealNum!=="")
				$SQLQuery .= "AND(Num = ".intval($this->DealNum).")";
			if($this->DealAnnot!=="")
				$SQLQuery .= "AND(Annot LIKE \"%".$this->Encode($this->DealAnnot)."%\")";
			if($this->DealName!=="")
				$SQLQuery .= "AND(Name LIKE \"%".$this->Encode($this->DealName)."%\")";
			$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
			mysql_select_db($this->Database,$Connection);
			$RowFoundId = "";
			$RowFoundNum = "";
			$RowFoundType = "";
			$RowRegisterId = ""	;
			$RowRegisterNum = "";
			$RowDealAnnot = "";
			$RowDealName = "";
			$RowDealNum = "";
			$RowDealId = "";
			$ResultBlock = "<table border=\"0\" style=\"font-weight:bold\" class=\"TableBlock1\">";
			$result = mysql_query($SQLQuery);
			while($row = mysql_fetch_array($result, MYSQL_NUM)){
				$RowDealId = $row[0];
				$RowDealNum = intval($row[1]);
				$RowFoundId = $row[3];
				$RowDealName = $this->Decode($row[2]);
				$RowDealAnnot = $this->Decode($row[5]);
				$RowRegisterId =  $row[4];
				$SQLQuery = "SELECT * FROM Founds WHERE (id = $RowFoundId)";
				$FoundSearchResult = mysql_query($SQLQuery);
				$FoundRow = mysql_fetch_array($FoundSearchResult, MYSQL_NUM);
				$RowFoundType = (intval($FoundRow[3])==0)?"":"checked";
				$RowFoundNum = intval($FoundRow[1]);
				$RowType = "";
				if($row[3] == "1")
					$RowType = "Checked";
				$SQLQuery = "SELECT * FROM Registers WHERE (id = $RowRegisterId)";
				$RegisterSearchResult = mysql_query($SQLQuery);
				$RegisterRow = mysql_fetch_array($RegisterSearchResult, MYSQL_NUM);
				$RowRegisterNum = intval($RegisterRow[1]);
				if($row[3] == "1")
					$RowType = "Checked";
				$ResultRow = "
				<tr>
					<td>
						<form action=\"$this->SiteURL\" id=\"DealEdit$RowDealId\" method=\"POST\">
						<input type=\"hidden\" name=\"Page\" value=\"Deals\">
						</form>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit$RowDealId\" 
						type=\"Number\" name=\"FoundId\" value=\"$RowFoundId\" placeholder=\"Id\">
					</td>
					<td>
						<div class=\"TableCaption3\">Тип</div>
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit$RowDealId\" 
						type=\"text\" name=\"RegisterId\" value=\"$RowRegisterId\" placeholder=\"Id\">
					</td>
					<td>
						<div class=\"TableCaption3\">Id</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Номер</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Имя</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Аннотация</div>
					</td>				
					<td>
						<div class=\"TableCaption3\">Действие</div>
					</td>
				</tr>
				<tr>	
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit$RowDealId\" 
						type=\"Number\" name=\"FoundNum\" value=\"$RowFoundNum\" placeholder=\"Номер\">
					</td>
					<td>
						<input  form=\"DealEdit\" 
						type=\"checkbox\" value=\"1\" name=\"FoundType\" $RowFoundType>Совр.
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit$RowDealId\" 
						type=\"text\" name=\"RegisterNum\" value=\"$RowRegisterNum\" placeholder=\"Номер\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit$RowDealId\" 
						type=\"text\" name=\"DealId\" value=\"$RowDealId\" placeholder=\"Id\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit$RowDealId\" 
						type=\"text\" name=\"DealNum\" value=\"$RowDealNum\" placeholder=\"Номер\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit$RowDealId\" 
						type=\"text\" name=\"DealName\" value=\"$RowDealName\" placeholder=\"Имя\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit$RowDealId\" 
						type=\"text\" name=\"DealAnnot\" value=\"$RowDealAnnot\" placeholder=\"Аннотация\">	
					</td>
					<td>
						<input class=\"InterfaceButton3\" form=\"DealEdit$RowDealId\" type=\"submit\" name=\"Action\" value=\"Изменить\">
						<input class=\"InterfaceButton4\" form=\"DealEdit$RowDealId\" type=\"submit\" name=\"Action\" value=\"Удалить\">
					</td>
				</tr>
				";
				$ResultBlock.=$ResultRow;
			}
			//mysql_free_result($result);
			//mysql_close($Connection);
			$ResultBlock .= "</table>";
		}
	}
	{$Block = "
		<form action=\"$this->SiteURL\" id=\"DealEdit\" method=\"POST\">
			<input type=\"hidden\" name=\"Page\" value=\"Deals\">
		</form>
		<div id=\"ErrorCaption\">
			$Error
		</div>
		<div id=\"FoundEditor\" style=\"font-weight:bold;\" class=\"TableBlock1\">
			<div style=\"text-align:center;\" class=\"TableCaption2\">
				Фонд
			</div>
			<table border=\"0\">
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit\" 
						type=\"text\" name=\"FoundId\" placeholder=\"Id\">
					</td>
					<td>
						<div class=\"TableCaption3\">Тип</div>
					</td>
				</tr>
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit\" 
						type=\"text\" name=\"FoundNum\" placeholder=\"Номер\">
					</td>
					<td>
						<input  form=\"DealEdit\" 
						type=\"checkbox\" value=\"1\" name=\"FoundType\" checked>Совр.
					</td>
				</tr>
			</table>
		</div>
		<div id=\"RegisterEditor\" style=\"font-weight:bold;\" class=\"TableBlock1\">
			<div style=\"text-align:center;\" class=\"TableCaption2\">
				Опись
			</div>
			<table border=\"0\">
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit\" 
						type=\"text\" name=\"RegisterId\" placeholder=\"Id\">
					</td>
				</tr>
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit\" 
						type=\"text\" name=\"RegisterNum\" placeholder=\"Номер\">
					</td>
				</tr>
			</table>
		</div>
		<div id=\"DealEditor\" style=\"font-weight:bold;\" class=\"TableBlock1\">
			<div style=\"text-align:center;\" class=\"TableCaption2\">
				Дело
			</div>
			<table border=\"0\">
				<tr>
					<td>
						<div class=\"TableCaption3\">Id</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Номер</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Имя</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Аннотация</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Действие</div>
					</td>
				</tr>
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit\" 
						type=\"text\" name=\"DealId\" placeholder=\"Id\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit\" 
						type=\"text\" name=\"DealNum\" placeholder=\"Номер\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit\" 
						type=\"text\" name=\"DealName\" placeholder=\"Имя\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"DealEdit\" 
						type=\"text\" name=\"DealAnnot\" placeholder=\"Аннотация\">	
					</td>
					<td>
						<input class=\"InterfaceButton3\" form=\"DealEdit\" type=\"submit\" name=\"Action\" value=\"Найти\">
						<input class=\"InterfaceButton4\" form=\"DealEdit\" type=\"submit\" name=\"Action\" value=\"Добавить\">
					</td>
				</tr>
			</table>
		</div>
		<div id=\"SearchResult\">
			$ResultBlock
		</div>
		";}
	return $Block;
}
//проверка существования такого дела
private function CheckDealExistance($FoundId, $RegisterId, $DealNum){
	$Result = false;
	$SQLQuery = "SELECT * FROM Deals WHERE (Register = ".intval($RegisterId).")AND(Found = ".intval($FoundId).")AND(Num =\"".intval($DealNum)."\")";
	$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
	mysql_select_db($this->Database,$Connection);
	$result = mysql_query($SQLQuery);
	$Found = mysql_affected_rows();
	if($Found > 0)
		$Result = true;
	mysql_close($Connection);
	return $Result;
}
//проверка существования такой описи
private function CheckRegisterExistance($FoundId, $RegisterNum){
	$Result = false;
	$SQLQuery = "SELECT * FROM Registers WHERE (Num = ".intval($RegisterNum).")AND(Found = ".intval($FoundId).")";
	$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
	mysql_select_db($this->Database,$Connection);
	$result = mysql_query($SQLQuery);
	$Found = mysql_affected_rows();
	if($Found > 0)
		$Result = true;
	mysql_close($Connection);
	return $Result;
}
//проверка существования такого фонда
private function CheckFoundExistance($FoundNum, $FoundType){
	$Result = false;
	$SQLQuery = "SELECT * FROM Founds WHERE (Num = ".intval($FoundNum).")AND(Type = ".intval($FoundType).")";
	$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
	mysql_select_db($this->Database,$Connection);
	$result = mysql_query($SQLQuery);
	$Found = mysql_affected_rows();
	if($Found > 0)
		$Result = true;
	mysql_close($Connection);
	return $Result;
}
//Форма добавления \ удаления фонда
private function FormFoundEdit(){
	$Error = "";
	$ResultBlock = "";
	$this->FoundId = (isset($_POST["FoundId"]))?$_POST["FoundId"]:"";
	$this->FoundNum = (isset($_POST["FoundNum"]))?$_POST["FoundNum"]:"";
	$this->FoundName = (isset($_POST["FoundName"]))?$_POST["FoundName"]:"";
	$this->FoundType = (isset($_POST["FoundType"]))?$_POST["FoundType"]:"0";
	$this->FoundCathegory = (isset($_POST["FoundCathegory"]))?$_POST["FoundCathegory"]:"";
	$this->Action = (isset($_POST["Action"]))?$_POST["Action"]:"";
	if ($this->Action !== ""){
		if($this->Action == "Удалить"){
			$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
			mysql_select_db($this->Database,$Connection);	
			$SQLQuery = "DELETE FROM Founds WHERE (Id =".intval($this->FoundId).")";
			$result = mysql_query($SQLQuery);
			mysql_close($Connection);
		}
		if($this->Action == "Изменить"){
			if(($this->FoundName == "")||($this->FoundType == "")||($this->FoundNum == "")||($this->FoundCathegory == "")){
			}else{
				if($this->FoundId !== ""){
					$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
					mysql_select_db($this->Database,$Connection);	
					$SQLQuery = "UPDATE Founds SET  Num =";
					$SQLQuery .=intval($this->FoundNum);
					$SQLQuery .=", Name = \"".$this->Encode($this->FoundName);
					$SQLQuery .="\", Type = ".intval($this->FoundType);
					$SQLQuery .=", Cathegory = ".intval($this->FoundCathegory)."";
					$SQLQuery .= " WHERE (Id = ".$this->FoundId.")";
					$result = mysql_query($SQLQuery);
					mysql_close($Connection);
				}
			}
		}
		if($this->Action == "Добавить"){
			if(($this->FoundName == "")||($this->FoundType == "")||($this->FoundNum == "")||($this->FoundCathegory == "")){
			}else{
				if($this->FoundId !== ""){
					$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
					mysql_select_db($this->Database,$Connection);	
					$SQLQuery = "UPDATE Founds SET  Num =";
					$SQLQuery .=intval($this->FoundNum);
					$SQLQuery .=", Name = \"".$this->Encode($this->FoundName);
					$SQLQuery .="\", Type = ".intval($this->FoundType);
					$SQLQuery .=", Cathegory = ".intval($this->FoundCathegory)."";
					$SQLQuery .= " WHERE (Id = ".$this->FoundId.")";
					$result = mysql_query($SQLQuery);
					mysql_close($Connection);
				} else{
					if(!$this->CheckFoundExistance($this->FoundNum,$this->FoundType)){
						$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
						mysql_select_db($this->Database,$Connection);	
						$SQLQuery = "INSERT INTO Founds (Num, Name, Type, Cathegory) values (";
						$SQLQuery .=intval($this->FoundNum);
						$SQLQuery .=", \"".$this->Encode($this->FoundName);
						$SQLQuery .="\", ".intval($this->FoundType);
						$SQLQuery .=", ".$this->FoundCathegory.")";
						$result = mysql_query($SQLQuery);
						mysql_close($Connection);
					}else $Error = "Уже существует!";
				}
			}
		}
		if($this->Action == "Найти"){
			$SQLQuery = "SELECT * FROM founds WHERE (1 = 1)";
			if($this->FoundNum!==""){
				$SQLQuery .= "AND(Num = ".intval($this->FoundNum).")";
			}
			if((($this->FoundType!=="")&&($this->FoundCathegory!=="0"))||(($this->FoundType!=="")&&($this->FoundName!==""))||(($this->FoundType!=="")&&($this->FoundNum!==""))){
				$SQLQuery .= "AND(Type = ".intval($this->FoundType).")";
			}
			if($this->FoundName!==""){
				$SQLQuery .= "AND(Name LIKE \"%".$this->Encode($this->FoundName)."%\")";
			}
			if($this->FoundCathegory!=="0"){
				$SQLQuery .= "AND(Cathegory = ".intval($this->FoundCathegory).")";
			}
			$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
			mysql_select_db($this->Database,$Connection);
			$RowId = "";
			$RowNumber = "";
			$RowName = "";
			$RowType = "";
			$RowCathegory = "";
			$ResultBlock = "<table border=\"0\">";
			$result = mysql_query($SQLQuery);
			while($row = mysql_fetch_array($result, MYSQL_NUM)){
				$RowId = $row[0];
				$RowNumber = $row[1];
				$RowCathegory = $row[4];
				$RowName = $this->Decode($row[2]);
				$RowType = "";
				if($row[3] == "1")
					$RowType = "Checked";
				$ResultRow = "
				<tr>
					<td>
						<form id=\"FoundResult$RowId\"	action=\"$this->SiteURL\" method=\"POST\">
							<input form=\"FoundResult$RowId\" 
						type=\"Hidden\" name=\"Page\" value=\"Founds\">
						</form>
						<input class=\"TextInputNoLabel2\" form=\"FoundResult$RowId\" 
						type=\"Number\" name=\"FoundId\" value=\"$RowId\" placeholder=\"Id\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"FoundResult$RowId\" 
						type=\"Number\" name=\"FoundNum\" value=\"$RowNumber\" placeholder=\"Number\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"FoundResult$RowId\" 
						type=\"text\" name=\"FoundName\" value=\"$RowName\" placeholder=\"Name\">
					</td>
					<td>
						<select class=\"TextInputNoLabel2\" name=\"FoundCathegory\" form=\"FoundResult$RowId\" >
						".$this->FormSearchCathegories($RowCathegory)."
						</select>
					</td>
					<td>
						<input  form=\"FoundResult$RowId\" 
						type=\"checkbox\" value=\"1\" name=\"FoundType\" $RowType>Совр.
					</td>
					<td>
						<input class=\"InterfaceButton3\" form=\"FoundResult$RowId\" type=\"submit\" name=\"Action\" value=\"Изменить\">
						<input class=\"InterfaceButton4\" form=\"FoundResult$RowId\" type=\"submit\" name=\"Action\" value=\"Удалить\">
					</td>
				</tr>";
				$ResultBlock.=$ResultRow;
			}
			//mysql_free_result($result);
			//mysql_close($Connection);
			$ResultBlock .= "</table>";
		}
	}
	$Block = "
		<div id=\"ErrorCaption\">
			$Error
		<div>
		<form action=\"$this->SiteURL\" id=\"FoundEdit\" method=\"POST\">
			<input type=\"hidden\" name=\"Page\" value=\"Founds\">
		</form>
		<div id=\"FoundEditor\" style=\"font-weight:bold;\" class=\"TableBlock1\">
			<div style=\"text-align:center;\" class=\"TableCaption2\">
					Фонд
			</div>
			<table border=\"0\">
				<tr>
					<td>
						<div class=\"TableCaption3\">Id</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Номер</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Имя</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Категория</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Тип</div>
					</td>
					<td>
						<div class=\"TableCaption3\">Действие</div>
					</td>
				</tr>
				<tr>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"FoundEdit\" 
						type=\"Number\" name=\"FoundId\" placeholder=\"Id\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"FoundEdit\" 
						type=\"Number\" name=\"FoundNum\" placeholder=\"Номер\">
					</td>
					<td>
						<input class=\"TextInputNoLabel2\" form=\"FoundEdit\" 
						type=\"text\" name=\"FoundName\" placeholder=\"Имя\">
					</td>
					<td>
						<select class=\"TextInputNoLabel2\" name=\"FoundCathegory\" form=\"FoundEdit\" >
						".$this->FormSearchCathegories(0)."
						</select>
					</td>
					<td>
						<input  form=\"FoundEdit\" 
						type=\"checkbox\" value=\"1\" name=\"FoundType\" checked>Совр.
					</td>
					<td>
						<input class=\"InterfaceButton3\" form=\"FoundEdit\" type=\"submit\" name=\"Action\" value=\"Найти\">
						<input class=\"InterfaceButton4\" form=\"FoundEdit\" type=\"submit\" name=\"Action\" value=\"Добавить\">
					</td>
				</tr>
			</table>
		</div>
		<br>
		<div style=\"margin-top:5px\" id=\"ResultBlock\" class=\"TableBlock1\">
			$ResultBlock
		</div>
		";
	return $Block;
}
//Форма добавления \ удаления категории
private function FormCathegoryEdit(){
	$Block = $this->Form404();
	if($this->Username!==""){
	$this->Action = ((isset($_POST["Action"]) ? $_POST["Action"]:"")=="")? 
		   (isset($_COOKIE["Action"]) ? $_COOKIE["Action"]:""):$_POST["Action"];
	$Action = $this->Action;
	$CathegoryName = isset($_POST["CathegoryName"]) ? $_POST["CathegoryName"]:"";
	$CathegoryId = isset($_POST["CathegoryId"]) ? $_POST["CathegoryId"]:"";
	if (($Action == "Добавить")&&($CathegoryName!=="")){
		$SQLQuery = "INSERT INTO $this->Database.Cathegories (Name) values (\"".$this->encode($CathegoryName)."\")";
		$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
		mysql_select_db($this->Database,$Connection);	
		$result = mysql_query($SQLQuery	);
		mysql_close($Connection);
	}
	if ((($Action == "Удалить")&&($CathegoryId!==""))){
		$SQLQuery = "DELETE FROM $this->Database.Cathegories WHERE ( id = \"".intval($CathegoryId)."\")";
		$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
		mysql_select_db($this->Database,$Connection);	
		$result = mysql_query($SQLQuery	);
		mysql_close($Connection);
	}
	$Action = $this->Action;
	$Block = "
	<div id=\"CathegoryEditorBlock\" >
	<div id=\"CathegoryEditorCaption\" >
			Редактор Категорий
	</div>
	<div id=\"CathegoryEditor\" >
			<form id=\"AddCathegory\" action=\"$this->SiteURL\" method=\"POST\">
			<input type=\"hidden\" name=\"Page\" value=\"Cathegories\">
			</form>
			<form id=\"DeleteCathegory\" action=\"$this->SiteURL\" method=\"POST\">
			<input type=\"hidden\" name=\"Page\" value=\"Cathegories\">
			</form>
			<table style=\"\">
				<tr>
					<td>
						<input class=\"TextInputNoLabel1\" form=\"AddCathegory\" type=\"text\" name=\"CathegoryName\" value=\"\">
						<br>
						<select class=\"TextInputNoLabel1\" form=\"DeleteCathegory\" name=\"CathegoryId\">
							".$this->FormSearchCathegories(0)."
						</select>
					</td>
					<td>
					<input class=\"InterfaceButton2\" form=\"AddCathegory\" type=\"submit\" name=\"Action\" value=\"Добавить\">
					<br>
					<input class=\"InterfaceButton2\" form=\"DeleteCathegory\" type=\"submit\" name=\"Action\" value=\"Удалить\">							
					</td>
				</tr>
			</table>
		</div>
	</div>
	";
	}
	return $Block;
}
//Страница поиска
private function FormSearchForm(){
		$this->ListNum = (isset($_POST["ListNum"]))?$_POST["ListNum"]:"";
		$this->ListName = (isset($_POST["ListName"]))?$_POST["ListName"]:"";
		$this->DealNum = (isset($_POST["DealNum"]))?$_POST["DealNum"]:0;
		$this->DealName = (isset($_POST["DealName"]))?$_POST["DealName"]:"";
		$this->RegisterNum = (isset($_POST["RegisterNum"]))?$_POST["RegisterNum"]:0;
		$this->RegisterName = (isset($_POST["RegisterName"]))?$_POST["RegisterName"]:"";
		$this->FoundNum = (isset($_POST["FoundNum"]))?$_POST["FoundNum"]:"";
		$this->FoundName = (isset($_POST["FoundName"]))?$_POST["FoundName"]:"";
		$this->FoundType = (isset($_POST["FoundType"]))?$_POST["FoundType"]:0;
		$this->FoundCathegory = (isset($_POST["FoundCathegory"]))?$_POST["FoundCathegory"]:"";
		$this->SearchCurrentElement = (isset($_POST["SearchCurrentElement"]))?$_POST["SearchCurrentElement"]:1;
		$this->TagLine = (isset($_POST["TagLine"]))?$_POST["TagLine"]:"";
		$this->Catalog = (isset($_POST["Catalog"]))?$_POST["Catalog"]:"0";
		$this->DealId= (isset($_POST["DealId"]))?$_POST["DealId"]:0;
		$this->RegisterId= (isset($_POST["RegisterId"]))?$_POST["RegisterId"]:0;
		$this->FoundId= (isset($_POST["FoundId"]))?$_POST["FoundId"]:0;
		$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
		mysql_select_db($this->Database,$Connection);
		if($this->TagLine == ""){
		$queryFound = "SELECT * FROM Founds WHERE (1=1) ";
		$queryRegister = "SELECT * FROM Registers WHERE (1=1) ";
		$queryDeal = "SELECT * FROM Deals WHERE (1=1) ";
		$queryList = "SELECT * FROM Lists WHERE (1=1) ";
		$FoundList = "AND( ";
		$RegisterList = "AND( ";
		$DealList = "AND( ";
		if(($this->FoundNum !=="")&&($this->FoundNum!== 0)&&($this->FoundNum!=="0"))
		if(is_numeric($this->FoundNum)&&is_numeric($this->FoundType)){
			$queryFound .= "AND(Num = \"$this->FoundNum\") AND (Type = $this->FoundType)";
		}
		if(is_numeric($this->FoundCathegory)){
			if(($this->FoundCathegory!== 0)&&($this->FoundCathegory!=="0")&&($this->FoundCathegory!==""))
			$queryFound .= "AND (Cathegory = ".intval($this->FoundCathegory).")" ;
		}		
		if($this->FoundName!==""){
			$queryFound .= "AND (Name LIKE \"%".$this->Encode($this->FoundName)."%\")"; 
		}
		if(is_numeric($this->RegisterNum)){
			$queryRegister .= "AND(Num = $this->RegisterNum)";
		}
		if($this->RegisterName!==""){
			$queryRegister .= "AND (Name LIKE \"%".$this->Encode($this->RegisterName)."%\")"; 
		}
		if(is_numeric($this->DealNum)){
			$queryDeal .= "AND(Num = $this->DealNum)";
		}
		if($this->DealName!==""){
			$queryDeal .= "AND (Name LIKE \"%".$this->Encode($this->DealName)."%\")"; 
		}
		if(is_numeric($this->ListNum)){
			$queryList .= "AND(Num = $this->ListNum)";
		}
		if($this->ListName!==""){
			$queryList .= "AND (Name LIKE \"%".$this->Encode($this->ListName)."%\")"; 
		}
		$result = mysql_query($queryFound);
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$id = intval($row[0]);
			if($FoundList!=="AND( "){$FoundList .= " OR ";}
			$FoundList .="(Found = $id) ";
		}
		if ( $FoundList == "AND( " ){
			$FoundList .= " 1 = 1 ";
		}
		$FoundList .= ")";
		$queryRegister .= $FoundList;
		mysql_free_result($result);
		$result = mysql_query($queryRegister);
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$id = intval($row[0]);
			if($RegisterList!=="AND( "){$RegisterList .= " OR ";}
			$RegisterList .="(Register = $id) ";
		}
		if ( $RegisterList == "AND( " ){
			$RegisterList .= " 1 = 1 ";
		}
		$RegisterList .= ")";
		$queryDeal .= $FoundList;
		mysql_free_result($result);
		$result = mysql_query($queryDeal);
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$id = intval($row[0]);
			if($DealList!=="AND( "){$DealList .= " OR ";}
			$DealList .="(Deal = $id) ";
		}
		if ( $DealList == "AND( " ){
			$DealList .= " 1 = 1 ";
		}
		$DealList .= ")";
		$queryList .= $FoundList;
		$queryList .= $RegisterList;
		$queryList .= $DealList;
		mysql_free_result($result);
		$result = mysql_query($queryList);
		$this->SearchMaxElement = mysql_affected_rows();
		if($this->SearchMaxElement == -1){
			$this->SearchMaxElement = 1;
		}
		}else{
		$queryFound = "SELECT * FROM Founds WHERE (1=1) ";
		$queryRegister = "SELECT * FROM Registers WHERE (1=1) ";
		$queryDeal = "SELECT * FROM Deals WHERE (1=1) ";
		$queryList = "SELECT * FROM Lists WHERE (1=1) ";
		$FoundList = "OR( ";
		$RegisterList = "OR( ";
		$DealList = "OR( ";
		$queryFound .= "AND (Name LIKE \"%".$this->Encode($this->TagLine)."%\")"; 
		$queryRegister .= "AND (Name LIKE \"%".$this->Encode($this->TagLine)."%\")"; 
		$queryDeal .= "AND (Name LIKE \"%".$this->Encode($this->TagLine)."%\")"; 
		$queryList .= "AND (Name LIKE \"%".$this->Encode($this->TagLine)."%\")";
		$result = mysql_query($queryFound);
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$id = intval($row[0]);
			if($FoundList!=="OR( "){$FoundList .= " OR ";}
			$FoundList .="(Found = $id) ";
		}
		if ( $FoundList == "OR( " ){
			$FoundList .= " 0 = 1 ";
		}
		$FoundList .= ")";
		mysql_free_result($result);
		$result = mysql_query($queryRegister);
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$id = intval($row[0]);
			if($RegisterList!=="OR( "){$RegisterList .= " OR ";}
			$RegisterList .="(Register = $id) ";
		}
		if ( $RegisterList == "OR( " ){
			$RegisterList .= " 0 = 1 ";
		}
		$RegisterList .= ")";
		mysql_free_result($result);
		$result = mysql_query($queryDeal);
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$id = intval($row[0]);
			if($DealList!=="OR( "){$DealList .= " OR ";}
			$DealList .="(Deal = $id) ";
		}
		if ( $DealList == "OR( " ){
			$DealList .= " 0 = 1 ";
		}
		$DealList .= ")";
		$queryList .= $FoundList;
		$queryList .= $RegisterList;
		$queryList .= $DealList;
		mysql_free_result($result);
		$result = mysql_query($queryList);
		$this->SearchMaxElement = mysql_affected_rows();
		if($this->SearchMaxElement == -1){
			$this->SearchMaxElement = 1;
		}		
		}
		if ((isset($_POST["Slide"]))&&($_POST["Slide"]=="->")){
			$Step = ($this->SearchCurrentElement<$this->SearchMaxElement)?1:0;			
			$this->SearchCurrentElement += $Step;
		}	
		if ((isset($_POST["Slide"]))&&($_POST["Slide"]=="<-")){
			$Step = ($this->SearchCurrentElement>1)?1:0;
			$this->SearchCurrentElement -= $Step;
		}
		if ((isset($_POST["Slide"]))&&($_POST["Slide"]=="<<=")){
			$this->SearchCurrentElement = 1;
		}
		if ((isset($_POST["Slide"]))&&($_POST["Slide"]=="=>>")){
			$this->SearchCurrentElement = $this->SearchMaxElement;
		}
		$CurrentElement = $this->SearchCurrentElement;
		$MaxElement = $this->SearchMaxElement;
		for($i=1;(($i <= $CurrentElement)&&($i<=$MaxElement));$i++){
			$row = mysql_fetch_array($result, MYSQL_NUM);
		}
		$Path = $this->Decode($row[6]);
		$FoundId = $row[3];
		$RegisterId = $row[4];
		$DealId = $row[5];
		$ListId = $row[0];
		mysql_close($Connection);
		$Path = str_replace("\\", "/", $Path);
		$SearchForm = "
		<div id=\"SearchFormBox\">
			<div id=\"SearchCaption\">
				Поиск листов по базе данных
			</div>
			<div id=\"SearchForm\">
			<div id=\"SearchControls\">
			".$this->FormTagSearchLine()."
			<div id=\"SearchSlideTop\">
				".$this->FormSearchPanel()."
			</div>
			<div id=\"SearchResultPicture\">
				".$this->FormSearchResultPicture($Path)."
			</div>
			<div id=\"SearchResultInformation\">
				".$this->FormSearchResultInformation($FoundId, $RegisterId, $DealId, $ListId)."
			</div>
			<div id=\"SearchSlideBottom\">
			   ".$this->FormSearchPanel()."
			</div>
			</div>
			".$this->FormTagSearchLine()."
			<div id=\"SearchPanel\">
				".$this->FormSearchFrame()."
			</div>
		</div>
		</div>
		";
	return $SearchForm;
}
//строка поиска по тегам
private function FormTagSearchLine(){
	$Block = "
	<div id=\"TagSearchLine\">
		<form method=\"post\" action=\"$this->SiteURL\">
			<input type=\"hidden\" name=\"Page\" value=\"View\">
			<input type=\"text\" id=\"TagLine\" name=\"TagLine\" placeholder=\"Поиск по ключевым словам\">
			<input type=\"submit\" class=\"InterfaceButton2\" name=\"Action\" value=\"Найти\">
		</form>
	</div>
	";
	return $Block;
}
//информация о результате поиска: формировалка фрейма
private function FormSearchResultInformation($FoundId,$RegisterId,$DealId,$ListId){
	$Connection = mysql_connect($this->Host, $this->RootUsername, $this->RootPswd);
	mysql_select_db($this->Database,$Connection);	
	$queryFound = "SELECT * FROM Founds WHERE (id = \"$FoundId\")";
	$queryRegister = "SELECT * FROM Registers WHERE (id = \"$RegisterId\")";
	$queryDeal = "SELECT * FROM Deals WHERE (Id = \"$DealId\")";
	$queryList = "SELECT * FROM Lists WHERE (Id = \"$ListId\")";
	$Cathegory = "";
	$result = mysql_query($queryFound);
	$Found = mysql_fetch_array($result, MYSQL_NUM);
	mysql_free_result($result);
	$result = mysql_query($queryRegister);
	$Register = mysql_fetch_array($result, MYSQL_NUM);
	mysql_free_result($result);
	$result = mysql_query($queryDeal);
	$Deal = mysql_fetch_array($result, MYSQL_NUM);
	mysql_free_result($result);
	$result = mysql_query($queryList);
	$List = mysql_fetch_array($result, MYSQL_NUM);
	mysql_free_result($result);
	$FoundType = ($Found[3]==1)? "Совр.":"Дорев.";
	if(isset($Found[4])){
		$queryCathegory = "SELECT * FROM Cathegories WHERE (id = $Found[4])";
		$result = mysql_query($queryCathegory);
		$CathegoryResult = mysql_fetch_array($result, MYSQL_NUM);
		$Cathegory = $this->Decode($CathegoryResult[1]);
		mysql_free_result($result);
	}
	mysql_close($Connection);
	return "
			<center>
				<div id=\"SearchResultInformationBox\">	
					<table border=\"1\">
						<tr>
							<td style=\"width:32%\">
								Номер фонда:
							</td>
							<td style=\"width:18%\">
								".$Found[1]."
							</td>
							<td style=\"width:28%\">
								Имя фонда:
							</td>
							<td style=\"width:22%\">
								".$this->Decode($Found[2])."
							</td>
						</tr>
						<tr>
							<td>
								Тип фонда:
							</td>
							<td>
								$FoundType
							</td>
							<td>
								Категория:
							</td>
							<td>
								$Cathegory
							</td>
						</tr>
						<tr>
							<td>
								Номер описи:
							</td>
							<td>
								".$Register[1]."
							</td>
							<td>
								Имя описи:
							</td>
							<td>
								".$this->Decode($Register[3])."
							</td>
						</tr>
						<tr>
							<td>
								Номер дела:
							</td>
							<td>
								".$Deal[1]."
							</td>
							<td>
								Имя дела:
							</td>
							<td>
								".$this->Decode($Deal[2])."
							</td>
						</tr>
						<tr>
							<td>
								Номер листа:
							</td>
							<td>
								".$List[1]."
							</td>
							<td>
								Имя листа:
							</td>
							<td>
								".$this->Decode($List[2])."
							</td>
						</tr>
					</table>
					<table style=\"width:100%\" border=\"1\">
						<tr >
							<td style=\"width:50%\">
								Аннотация к Описи
							</td>
							<td>
								".$this->Decode($Register[4])."
							</td>
						</tr>
						<tr>
							<td>
								Аннотация к Делу
							</td>
							<td>
								".$this->Decode($Deal[5])."
							</td>
						</tr>
					</table>
				</div>
			</center>
		";
}
//залить файл
private function UploadImage(){
	$UploadDir = "\\pictures\\";
	$UploadFile = $UploadDir . basename($_FILES['UserImage']['name']);
	move_uploaded_file($_FILES['UserImage']['tmp_name'], $uploadfile);
	$Date = date("YmdHis", strtotime($_REQUEST["DateOfRequest"])); 
}
//создать 1 результат
private function FormSearchResultPicture($Path){
	return "<img style=\"height:480px;width:640px;\" src=\"".$this->SiteURL."/pictures/".$Path."\">";
}
//формируем форму просмотра формы формы - шучу
private function FormEditPage(){
	$EditPage = "
	<div id=\"EditPage\">
		<div id=\"AddForms\">
			<div  class=\"TextLabelNoEditBox5\">
				Действия с базой.
			</div>
			<center>
			<button class=\"InterfaceButton6\" onclick=\"wind=window.open('$this->SiteURL/?Page=Cathegories','Редактор категорий','width=210,height=110,resizable=no,scrollbars=no,status=yes');wind.focus();\" >Категории</button>
			<br>
			<button class=\"InterfaceButton6\" onclick=\"wind=window.open('$this->SiteURL/?Page=Founds','Редактор Фондов','width=610,height=210,resizable=no,scrollbars=no,status=yes');wind.focus();\" >Редактор Фондов</button>
			<br>
			<button class=\"InterfaceButton6\" onclick=\"wind=window.open('$this->SiteURL/?Page=Registers','Редактор Описей','width=820,height=250,resizable=no,scrollbars=no,status=yes');wind.focus();\" >Редактор Описей</button>
			<br>
			<button class=\"InterfaceButton6\" onclick=\"wind=window.open('$this->SiteURL/?Page=Deals','Редактор Дел','width=780,height=210,resizable=no,scrollbars=no,status=yes');wind.focus();\" >Редактор Дел</button>
			<br>
			<button class=\"InterfaceButton6\" onclick=\"wind=window.open('$this->SiteURL/?Page=Lists','Редактор Листов','width=700,height=210,resizable=no,scrollbars=no,status=yes');wind.focus();\" >Редактор Листов</button>
			</center>
		</div>
	</div>
	";
	return $EditPage;
}
//формируем страницу и переход между страницами
//панельными ссылками сформируем переход через get
//Main, View, Catalog, Internal, Profile, Edit 
//зы надо тонну защит от SQL иньекции
private function FormPage(){
//формирователь страниц
//если не залогинен: выкинуть на мейн, если залогинен на интернал
	$Page = ($this->Page == "")?(($this->Username=="")?"Main":"Internal"):$this->Page;
	if($this->Username!==""){
		switch ($Page){
			case "Main": return $this->FormMainPage();
				break;
			case "View": return $this->FormViewPage();
				break;
			case "Internal": return $this->FormInternalPage();
				break;
			case "Profile": return $this->FormProfilePage();
				break;
			case "Edit": return $this->FormEditPage();
				break;
			case "Catalog": return $this->FormCatalogPage();
				break;
			default:
				return $this->Form404();
	}}else{
		switch ($Page){
			case "Main": return $this->FormMainPage();
				break;
			case "View": return $this->FormViewPage();
				break;
			case "Catalog": return $this->FormCatalogPage();
				break;
			default: return $this->Form404();
		}
	}
}
//Проверка авторизации
private function LoginUser(){
	//быдлолололокод
	//многозубодробительнойлогики
	$this->Password = ((isset($_POST["Password"]) ? $_POST["Password"]:"")=="")? 
		(isset($_COOKIE["Password"]) ? $this->Decode($_COOKIE["Password"]):""):$_POST["Password"];
	$this->Username = ((isset($_POST["Login"]) ? $_POST["Login"]:"")=="")? 
				(isset($_COOKIE["Login"]) ? $this->Decode($_COOKIE["Login"]):""):$_POST["Login"];
	$this->Action = ((isset($_POST["Action"]) ? $_POST["Action"]:"")=="")? 
					   (isset($_COOKIE["Action"]) ? $_COOKIE["Action"]:""):$_POST["Action"];
	//экшен нельзя послать геттом!
	//гетттолько на пейдж листатель, если не выйдет захайдить фичу в пост
	if($this->Username == ""){//Все также глухо?:D
		$this->Action = "Выйти"; //этот экшен дальше обработается и очистит и все
	}else{
		$this->Access = $this -> CheckUsername($this -> Username,$this -> Password);
		if(isset($_POST["Login"])){
			//код проверки капчи
			//срабатывает если юзернейм был послан по Post запросу
			if ($this->CaptchaCheck()==0){
				//если чек не прошел, то надо снять авторизацию 
				$this->Action = "Выйти";
				$this -> Access = 0;
				$this->Username = "";
			}
		}
		if($this -> Access == 0){
			$this->Action = "Выйти";
			$this->Username = "";
		}else{
			$this->AddCookie("Login", $this->Encode($this -> Username));
			$this->AddCookie("Password", $this->Encode($this -> Password));
			//$this->AddCookie("Action", $this -> Action);
			//$this->AddCookie("ActionString", $this -> ActionString);
			//$this->AddCookie("Page", "");
			//$this->AddCookie("Access", "");
		}
	}
	if($this->Action == "Выйти"){//нам сказали: ВЫЙТИ
		$this->Username = "";
		$this->Password = "";
		$this->Action = "";
		$this->ActionString = "";
		$this->Page = "";
		$this->Access = 0;
		$this->DeleteCookie("Login");
		$this->DeleteCookie("Password");
		$this->DeleteCookie("Action");
		$this->DeleteCookie("ActionString");
		$this->DeleteCookie("Page");
		$this->DeleteCookie("Access");
		//удаление кукисов
	}
	return;
}
//Смена пароля пользователя
private function ChangePassword(){
	$Password = $this->Password;
	$NewPassword = 	(isset($_POST["NewPassword"]) ? $_POST["NewPassword"]:"");
	$ConfirmPassword = 	(isset($_POST["PasswordConfirm"]) ? $_POST["PasswordConfirm"]:"");
	$Username = $this->Username;
	if(($Username!=="")&&($NewPassword == $ConfirmPassword)){
		$this->SetPassword($Username, $Password, $NewPassword);
	}
	return;
}
//обработка действий
private function CheckActions(){
	$this->Action = ((isset($_POST["Action"]) ? $_POST["Action"]:"")=="")? 
					   (isset($_COOKIE["Action"]) ? $_COOKIE["Action"]:""):$_POST["Action"];
	$Action = $this->Action;
	if($Action !== ""){
		switch ($Action){
			case "Сменить пароль": $this->ChangePassword();
				break;		
		}
	}
	return;
}
//проверка POST и Куки на авторизацию, вся работа с куки будет сделана заранее, до отправки хидеров (html5)
//основную обработку их напишу тут
private function CheckPosts(){
	$this->LoginUser();
	$this->CheckActions();
	return;
}
//формирователь самой капчи
private function FormCaptcha(){
	$string = "";
	for ($i = 0; $i < 3; $i++) 
		$string .= chr(rand(97, 122));
	$_SESSION["rand_code"] = $string; 
	$image = imagecreatetruecolor(170, 60); 
	$black = imagecolorallocate($image, 10, 110, 0); 
	$color = imagecolorallocate($image, 250, 20, 0); 
	$bg = imagecolorallocate($image, 235, 235, 235); 
	imagefilledrectangle($image,0,0,299,79,$bg); 
	imagettftext ($image, 30, 0, 10, 40, $color, "verdana.ttf", $_SESSION['rand_code']);
	header("Content-type: image/png");
	imagepng($image);
	return;
}
//простая проверка капчи
private function CaptchaCheck(){
	$Result = 0;
	if((isset($_POST["Captcha"]))&&(isset($_SESSION['rand_code']))){
		$Result = ($_POST["Captcha"]==$_SESSION['rand_code'])?1:0;
	}
	return $Result;
}
//Проверить не шифрованные передаваемые параметры управления
private function CheckGets(){
	$this->Page = ((isset($_POST["Page"]) ? $_POST["Page"]:"")=="")? 
		(isset($_GET["Page"]) ? $_GET["Page"]:""):$_POST["Page"];
	return;
}
//создание кукиса
private function AddCookie($Name,$Value){
	setcookie($Name, $Value, time()+36000);
	return;
}
//воизбежание ошибок: удаление кукиса
private function DeleteCookie($Name){
	setcookie($Name, "", time()-1);
	return;
}
//проверка авторизации: возвращаем
private function CheckUsername($Username, $Password){
//Функция напиленная во внешнем  модуле
	return $this -> Login($Username,$Password);
}
//Создадим форму авторизации
private function FormLoginBlock($Username){
//Если юзернейма нет, то вывести форму авторизации, если есть, то открыть
	if($Username == ""){
	$Block = "
<div id=\"LoginFrameBlock\" style=\" border-style: ridge;border-width:0px\" >	
	<form enctype=\"form-data\" method=\"post\" action=".$this->SiteURL.">
			<label id=\"LoginLabel\" class=\"LoginFrameLabel\" for=\"LoginInput\">Логин</label>
			<input id=\"LoginInput\" class=\"LoginFrameInput\" type=\"text\" size=\"15\"  name=\"Login\">
		<div id=\"LoginFrame\">
		</div>
		<div id=\"PasswordFrame\">	
			<label id=\"PasswordLabel\" class=\"LoginFrameLabel\" for=\"PasswordInput\">Пароль</label>
			<input id=\"PasswordInput\" class=\"LoginFrameInput\" type=\"password\" size=\"15\"  name=\"Password\">
		</div>
		Введите текст с картинки:
		<br>
		<div id=\"CaptchaFrame\">	
			<label class=\"CaptchaFrameLabel\"><img id=\"CaptchaImg\" onclick=\"document.getElementById('CaptchaImg').src = '?Page=Captcha&Code=' + Math.random()\" style=\"vertical-align: middle;\" width=\"45\" height=\"20\"  src=\"https://127.0.0.1/?Page=Captcha\"></img></label>
			<input id=\"CaptchaInput\" class=\"CaptchaFrameInput\" type=\"text\" size=\"15\"  name=\"Captcha\">
		</div>
		<div id=\"SubmitFrame\">	
			<input class=\"InterfaceButton1\" type=\"submit\" size=\"15\"  name=\"Action\" value=\"Войти\">
		</div>
	</form>
</div>";
	}else{
		$Block = "
<div id=\"LoginBlock\" style=\"border-style: ridge;border-width:0px\" >
	<form enctype=\"form-data\" method=\"post\" action=".$this->SiteURL.">
		<div id=\"AfterLoginCaption\">
		Здравствуйте, <button class=\"NavButton3\" form=\"NavForm\" type=\"submit\" name=\"Page\" value=\"Profile\">$Username</button>
		</div>
		<div id=\"AfterLoginText\">
		Если это не вы,
		<br>
		нажмите сюды:
		</div>
		<div id=\"SubmitFrame\">	
			<input Class=\"InterfaceButton1\" type=\"Submit\" size=\"30\" name=\"Action\" value=\"Выйти\">
		</div>
	</form>
</div>";
	}
	return $Block;
}
//Сформируем статью-блок: заголовок и основной текст
private function FormMainContentBlock($ContentName,$Head,$Article){
	$Block = "
	<article>
		<div id=\"".$ContentName."Article\">
		<header>
			<div id=\"$ContentName\" style=\"text-align: center;\">
				$Head
			</div>
		</header>
		<div id=\"$ContentName"."Information\" style=\"\">
				$Article
		</div>
		</div>
	</article>
	";
	return $Block;
}
//согласно html5 нам желательно выделить секцию главного контента страницы
private function FormMainContentSection($BlockContent){
	$Block = "
	<section>
		$BlockContent
	</section>";
	return $Block;
}
//тег: Боковая панель (будем скидывать все добро из нее на правую сторону)
//сформируем функцию заталкивающую содержимое в этот тег
private function FormSidePanel($BlockContent){
	$Block = "
	<aside>
		$BlockContent
	</aside>";
	return $Block;
}
//Навигационный блок
private function FormNavigationBlock($Head, $LinkList){
	$Block = "
	<nav>
		<div id=\"Navigation\" style=\"border-style: ridge;border-width:0px\">
			<div id=\"NavigationHead\" style=\"text-align: center;\">
				<label class=\"TextLabelNoEditBox3\">$Head</label>
			</div>
			<div id=\"NavigationList\" style=\"\">
				$LinkList
			</div>
		</div>
	</nav>";
	return $Block;
}
//Генератор блоков на сайте
private function FormBlock($BlockName,$BlockContent){
	$Block = "
	<div id=\"$BlockName\" style=\"width: 100%;border-style: ridge;border-width:2px;\">
		$BlockContent
	</div>";
	return $Block;
}
//распиливаем Jacket на центр, правую и левую колонку 20:60:20
private function FormJacket($Left, $Center, $Right){
$Jacket = "
	<table id=\"Jacket\"  style=\"width: 100%;border-style: ridge;border-width:0px;\">
		<tr valign=\"top\">
			<td  style=\"width: 10%;vertical-align: top;\">
				$Left
			</td>
			<td style=\"width: 60%;vertical-align: top;\">
				$Center
			</td>
			<td style=\"width: 10%;vertical-align: top;\">
				$Right
			</td>
		</tr>
	</table>
";
return $Jacket;
}
//Шапка сайта
private function FormHat($Text){
//Формирует окружающий шапку код
//тег Header нужен по валидатору Html5
	return "
<header>
	<div id=\"Hat\" style=\"text-align: center;\">
		<label class=\"TextLabelHatAndBoots\" >$Text</label>
	</div>
</header>
";
}
//На любые ноги нужны ботинки
private function FormBoots($Copyright,$Year){
	return "		
	<footer>
		 <div id=\"Boots\" style=\"text-align: center\">
				<div class=\"TextLabelHatAndBoots\" >$Copyright $Year</div>
		 </div>
	</footer>";
}
//Формируем таблицу разделяющую части сайта, как шапку, пальто и ботинки
private function FormCostume($Hat, $Jacket, $Boots){
	$Costume = "
<table id=\"Coat\" style=\"width: 100%;border-style: ridge;border-width:0px\" border=\"0\">
	<tr style=\"vertical-align: top;text-align: center;\">
		<td>
			$Hat
		</td>
	</tr>
	<tr style=\"vertical-align: top;text-align: center;\">
		<td>
			$Jacket
		</td>
	</tr>
	<tr style=\"vertical-align: top;text-align: center;\">
		<td>
			$Boots
		</td>
	</tr>
</table>
	";
	return $Costume;
}
//вывод всего на экран
private function Out($text){//вывод говнеца на экран (эхо некрасиво)
echo $text;
return;
}
//проверка на наличие SSL
private function CheckSSL(){
//немного ультраговнокода
	$Result = isset($_SERVER['HTTP_SCHEME']) ? $_SERVER['HTTP_SCHEME'] : (
     ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || 443 == $_SERVER['SERVER_PORT']
     ) ? 1 : 0);
	return $Result;
}
//Вывести ошибку, что нет SSL (МУХАХАХА)	
private function FormNoSSLError(){
	$Error = "
	<div id=\"Error\" style=\"text-align: center\">
		<label id=\"ErrorHead\">No SSL Error!</label>
		<br>
		<label id=\"ErrorText\">
		Извините, но использовать этот сайт без SSL невозможно.<br> 
		Прошу вас перейти по <a href=".$this->SSLRedirect." >ссылке</a>. 
		</label>
	</div>
	";
	return $Error;
}
//ошибка 404
private function Form404(){
	$Error = "
	<div id=\"Error\" style=\"text-align: center\">
		<label id=\"ErrorHead\">Ошибка 404</label>
		<br>
		<label id=\"ErrorText\">
		Извините, но этой страницы не существует.<br> 
		Прошу вас перейти по <a href=".$this->SSLRedirect." >ссылке</a>.
		</label>
	</div>
	";
	return $Error;
}
//Надо выделить генератор капчи
//для этого весь скрипт придется прогнать еще 1 раз
//надо выделить хрень, которая отделит рисование капчи от рисования веб
private function FormSelector(){
	$this->CheckPosts();
	$this->CheckGets();
	$this->Page = ((isset($_POST["Page"]) ? $_POST["Page"]:"")=="")? 
		(isset($_GET["Page"]) ? $_GET["Page"]:""):$_POST["Page"];
	switch ($this->Page){
	case "Captcha":{
		$this->FormCaptcha();
	}
	break;
	case "Cathegories":{
	if($this->Username!==""){
		$this->Out($this->FormHeader($this->SiteHead));
		$this->Out($this->FormCathegoryEdit());
		$this->Out($this->FormFooter());	
	}}
	break;
	case "Founds":{
	if($this->Username!==""){
		$this->Out($this->FormHeader($this->SiteHead));
		$this->Out($this->FormFoundEdit());
		$this->Out($this->FormFooter());	
	}}
	break;
	case "Registers":{
	if($this->Username!==""){
		$this->Out($this->FormHeader($this->SiteHead));
		$this->Out($this->FormRegisterEdit());
		$this->Out($this->FormFooter());	
	}}
	break;
	case "Deals":{
	if($this->Username!==""){
		$this->Out($this->FormHeader($this->SiteHead));
		$this->Out($this->FormDealEdit());
		$this->Out($this->FormFooter());	
	}}
	break;
	case "Lists":{
	if($this->Username!==""){
		$this->Out($this->FormHeader($this->SiteHead));
		$this->Out($this->FormListEdit());
		$this->Out($this->FormFooter());	
	}}
	break;
	default:{
		$this->SiteInternalPageText = "Данная страница доступна только после авторизации на сайте.<br> Перейти к 	<button class=\"NavButton2\" form=\"NavForm\" type=\"submit\" name=\"Page\" value=\"Edit\">Редактор</button> базы данных или к 	<button class=\"NavButton1\" form=\"NavForm\" type=\"submit\" name=\"Page\" value=\"View\">Просмотр</button>.";
		$this->Out($this->FormHeader($this->SiteHead));
		$this->Out($this->FormBody());
		$this->Out($this->FormFooter());
	}
	}
	return;
}
//Encode
private function Encode($Str){
	return base64_encode(urlencode($Str));
}
//Decode
private function Decode($Str){
	return urldecode(base64_decode($Str));
}
//Запуск сайта
public function Init(){
	$this->FormSelector();
	return;
}
}
//Require_Once ("Website.php");
Session_Start();
$Site = new Website();
$Site->Init(); 
?>
