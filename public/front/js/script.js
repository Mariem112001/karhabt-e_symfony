var message_valeur=document.querySelector(".information").children[1];
var cin,nom,prenom,region,date,monatant;
var valeur;
//CECI NOUS PERMET DE SELECTIONNER LE 2 EME PARAGRAHPE DANS LA DIV AYANT LA CLASS INFORMATION
window.onload=()=>{
    valeur="Aucune valeur"
    message_valeur.innerHTML=valeur;
}
document.forms[0].onchange=()=>{
    console.log("change");
}
var qr = new QRious({
    element: document.querySelector('.qrious'),
    size: 250,
    value: valeur
  });
function change(element) {
switch (element.name) {
    case "cin":
        cin=element.value;
      break;
    case "nom":
        nom=element.value
     break;
    case "prenom":
        prenom=element.value;    
    break;
    case "region":
        region=element.value;    
    break;
    case "date":
        date=element.value;
    break;
        case "montant":
        montnant=element.value;
        break;
   

    
}

valeur=cin+'-'+nom+'-'+prenom+'-'+region,+'-'+date+'-'+montant;
qr.value=valeur;
message_valeur.innerHTML=qr.value;


  
   
}



  