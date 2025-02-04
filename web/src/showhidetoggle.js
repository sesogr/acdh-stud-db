const showhidetoggle = (checkbox, ini=false) => {
  document
    .querySelectorAll("tr." + checkbox.dataset.dupeId)
    .forEach((el) => el.classList.toggle("hide"));
    
    const allcheckboxes = document.querySelectorAll('input[type="checkbox"][data-dupe-id]');

    const allcheckbox = document.querySelector('input[type="checkbox"]');
    if(ini){
        return null;
    };
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

  const allcheckboxes = document.querySelectorAll('input[type="checkbox"][data-dupe-id]');

  [...allcheckboxes].filter((e) => e.checked != checkbox.checked).forEach((e) => {
    e.checked = checkbox.checked
    showhidetoggle(e, true)}
  );

};