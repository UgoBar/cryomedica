const menuToggle = document.querySelectorAll('.menu-toggle'),
      mobileHeader = document.querySelectorAll('.mobile-header'),
      navItems = document.querySelectorAll('.mobile-nav-item'),
      navImg = document.querySelectorAll('.img-wrapper img'),
      slider = document.querySelectorAll('.app');

menuToggle[0].addEventListener('click', () => {

    toggleClass(slider, 'under-layered')

    // On simple node
    toggleClass(menuToggle, 'active');
    toggleClass(mobileHeader, 'active');

    // On Array
    toggleClass(navItems, 'active');
    toggleClass(navImg, 'active');
})

/**
 * Toggle class active on selected nodes
 * @param node
 * @param className
 */
const toggleClass = (node, className) => {
    node.forEach( item => {
        console.log(item)
        item.classList.toggle(className)
    })
}
