dropdownButtons = document.querySelectorAll('.dropdown');
dropdownContainers = document.querySelectorAll('.dropdown-container');

dropdownButtons.forEach((element, index) => {
    element.addEventListener("click", (e) => {
        if(dropdownContainers[index].classList.contains('hidden')) {
            dropdownContainers[index].classList.remove('hidden');
        } else {
            dropdownContainers[index].classList.add('hidden');
        }
    });
});