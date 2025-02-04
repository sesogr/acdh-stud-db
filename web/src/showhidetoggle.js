const showhidetoggle = (checkbox) => {
  document
    .querySelectorAll("tr." + checkbox.dataset.dupeId)
    .forEach((el) => el.classList.toggle("hide"));
    
    const allcheckboxes = document.querySelectorAll('input[type="checkbox"][data-dupe-id]');

    const allcheckbox = document.querySelector('input[type="checkbox"]');
    
    if( [...allcheckboxes].filter((e) => e.checked != checkbox.checked).length > 0 ){
        
        allcheckbox.indeterminate = true
        
    }else{

        allcheckbox.indeterminate = false
        allcheckbox.checked = checkbox.checked
    }
    
};


document.addEventListener("DOMContentLoaded", () =>
    document
      .querySelectorAll('input[type="checkbox"][data-dupe-id]')
      .forEach((cb) => !cb.checked && showhidetoggle(cb)),
  );
  

const changeall = (checkbox) => {
  let allstates = []
  
  const allinputs = document.querySelectorAll('input[type="checkbox"][data-dupe-id]');
  allstates = [...allinputs].map((e) => (e.checked));

  if (checkbox.checked) {
      for (let i = 0; i<allstates.length; i++){
          if (!allstates[i]){
              allinputs[i].click();
          }
      }
  }else{
      for (let i = 0; i<allstates.length; i++){
          if (allstates[i]){
              allinputs[i].click();
          }
      }
  };
};