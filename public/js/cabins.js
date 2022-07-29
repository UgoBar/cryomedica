const elecToggle   = document.querySelector('.toggle-elec'),
      azoteToggle  = document.querySelector('.toggle-azote');
      elecWrapper  = document.querySelector('.elec-wrapper');
      azoteWrapper = document.querySelector('.azote-wrapper');

elecToggle.addEventListener('click', (e) => {
    elecWrapper.classList.add('active');
    azoteWrapper.classList.remove('active');
});

azoteToggle.addEventListener('click', (e) => {
    elecWrapper.classList.remove('active');
    azoteWrapper.classList.add('active');
});
