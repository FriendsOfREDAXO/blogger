$(function() {
  const clangButtons = $('.blogger-form button[data-clang]')
  const contentAreas = $('.blogger-form section[data-clang]')

  const toggleContentArea = (clang) => {
    clangButtons.removeClass('btn-primary')
    $(`.blogger-form button[data-clang="${clang}"]`).addClass('btn-primary')

    contentAreas.removeClass('active').addClass('hidden')
    $(`.blogger-form section[data-clang="${clang}"]`).removeClass('hidden').addClass('active')
  }

  clangButtons.on('click', (event) => {
    event.preventDefault()
    const clang = event.target.getAttribute('data-clang')
    toggleContentArea(clang)
  })
})