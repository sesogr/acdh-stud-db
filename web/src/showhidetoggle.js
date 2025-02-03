const showhidetoggle = (checkbox) => {
  document
    .querySelectorAll("tr." + checkbox.dataset.dupeId)
    .forEach((el) => el.classList.toggle("hide"));
    
    const allcheckbox = document.querySelectorAll('input[type="checkbox"][data-dupe-id]')
    let indetermin = false
    allcheckbox.forEach((e) => {
        if (e.checked != checkbox.checked) {
            indetermin = true;
        }
    });
    document.querySelector('input[type="checkbox"]').indeterminate = false;
    if(indetermin){
        document.querySelector('input[type="checkbox"]').indeterminate = true;
    }
}

document.addEventListener("DOMContentLoaded", () =>
    document
      .querySelectorAll('input[type="checkbox"][data-dupe-id]')
      .forEach((cb) => !cb.checked && showhidetoggle(cb)),
  );
  

const changeall = (checkbox) => {
  let allstates = []
  
  const allinputs = document.querySelectorAll('input[type="checkbox"][data-dupe-id]');
  allinputs.forEach((e) => {
      allstates.push(e.checked)
  });
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

  checkbox.indeterminate = false;
};