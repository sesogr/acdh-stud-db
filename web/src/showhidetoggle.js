const showhidetoggle = (checkbox) =>
  document
    .querySelectorAll("tr." + checkbox.dataset.dupeId)
    .forEach((el) => el.classList.toggle("hide"));

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
              showhidetoggle(allinputs[i])
              allinputs[i].checked = true
          }
      }
  }else{
      for (let i = 0; i<allstates.length; i++){
          if (allstates[i]){
              showhidetoggle(allinputs[i])
              allinputs[i].checked = false
          }
      }
  };
};