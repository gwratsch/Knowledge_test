window.onload = function(){checkSelectOption();};
function checkSelectOption(){
    if(document.getElementById("dagboek-node-form") !== null){ 
        var titleContent =document.getElementById("edit-title").value;
		if(titleContent == ''){
			var d = new Date();
			var n = d.toLocaleDateString();
			document.getElementById("edit-title").value = n;
		}
    }
    if(document.getElementsByClassName("node-type-organisation")){
        addOnclickOption();
    }
}
window.onbeforeunload = function(){
    name = "saveInfo";
    nid = 0;
    if(document.getElementById('formRequest')){
        getInfo(name, nid);
    }
};
function getInfo(name, nid){
    var url = '/';
    requestName = name;
    requestNID=0;
    organisationNid = document.getElementById("organisationNID").innerHTML;
    nidRequest = document.getElementById("requestNID");
    if(nidRequest != null){requestNID = document.getElementById("requestNID").innerHTML;}
    organisationNid = Number(organisationNid);
    content = document.getElementById('organisationInfo').innerHTML;
    dataContent = buildDataContent(content);
    if(nid == 0){
        nid=requestNID;
    }
    data ="namerequest="+ requestName+
            "&content="+ dataContent+
            "&contentNid="+ requestNID+
            "&nameRequestNid="+nid+
            "&organisationNid="+organisationNid;
    posttextData(url, data)
     .then(function(result){
         if(name =='saveInfo'){
             alert("The information is saved.");
         }else if(name =='popup'){
             displayPopupWindow(result,requestName);
         }else{
            document.getElementById('organisationInfo').innerHTML = result;
         }
     })
}
function displayPopupWindow(result,requestName){
    var myWindow = window.open("", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=400,height=400");
    myWindow.document.write(result);
}
function getTextData(url, data){
    posttextData(url, data)
     .then(function(result){
         document.getElementById('organisationInfo').innerHTML = result;
     })
}
function saveTextData(url, data) {
    
    posttextData(url, data)
     .then(function(result){
         document.getElementById('organisationInfo').innerHTML = "0: "+result;
     })
}
function posttextData(url, data) {

  var result = fetch(url, {
    method: "post",
    body: data,
    cache: 'no-cache', 
    credentials: 'include', 
    headers: {
      'user-agent': 'Mozilla/4.0 MDN Example',
      'Accept': 'application/text',
      "Content-type": "application/x-www-form-urlencoded"
    },
   
    mode: 'cors', 
    redirect: 'follow', 
    referrer: 'no-referrer'
  })
  .then(response => response.text())
return result;
}
function addOnclickOption(){
    $onclickRows = document.getElementsByClassName("onclickreplace");
    for(var i=0;i<$onclickRows.length;i++){
       var $name = document.getElementsByClassName("onclickreplace")[i].innerHTML;
       $nodeId = document.getElementsByClassName("onclickreplace")[i].getAttribute("nid");
       document.getElementsByClassName("onclickreplace")[i].setAttribute("onclick", "getInfo('"+$name+"','"+$nodeId+"')");
       document.getElementsByClassName("onclickreplace")[i].setAttribute("class", "onclickreplace clickinfo");
    }
}
function buildDataContent(content){
    if(document.getElementById('formRequest')){
        nidInfo = document.getElementById('requestNID');
        nid=0;
        
        if(nidInfo != null){nid = nidInfo.innerHTML;}
        namerequestInfo = document.getElementById('requestName');
        namerequest='';
        if(namerequestInfo != null){namerequest = namerequestInfo.innerHTML;}
        divKeyFields = document.getElementsByClassName("requestRowKey");
        divValueFields = document.getElementsByClassName("requestRowValue");
        contentList=[divKeyFields.length];
        for(i=0;i<divKeyFields.length; i++){
           contentList[i] = {name: divKeyFields[i].innerHTML, nid: divKeyFields[i].getAttribute("nid") , content:divValueFields[i].value};
        }
        content = JSON.stringify(contentList);

    }
    return content;
}