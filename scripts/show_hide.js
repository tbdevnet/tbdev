function showhide(id){
  if (document.getElementById)
  {
    obj = document.getElementById(id);
    
    if (obj.style.display == "none")
    {
      obj.style.display = "";
    } 
    else 
    {
      obj.style.display = "none";
    }
  }
}