/**
 * Switching between languages
 */
$(function() {
  const clangButtons = $('.blogger-form button[data-clang]')
  const contentAreas = $('.blogger-form section[data-clang]')

  const toggleContentArea = (clang) => {
    clangButtons.removeClass('btn-primary')
    $(`.blogger-form button[data-clang="${clang}"]`).addClass('btn-primary')

    contentAreas.removeClass('active').addClass('hidden')
    const newArea = $(`.blogger-form section[data-clang="${clang}"]`).removeClass('hidden').addClass('active')
    newArea.find('input')[0].focus();
  }

  clangButtons.on('click', (event) => {
    event.preventDefault()
    const clang = event.target.getAttribute('data-clang')
    toggleContentArea(clang)
  })
})

/**
 * Check if datetime-local is available
 */
$(() => {
  const isAvail = () => {
    const input = document.createElement('input')
    input.setAttribute('type', 'datetime-local')
    return input.type === 'datetime-local'
  }

  if (isAvail() === false) {
    $('[type="datetime-local"]').each(function() {
      const dateTimeInput = $(this)
      const val = dateTimeInput.val().replace(/T/, ' ')
      dateTimeInput.val(val)
    })
  }
})

/**
 * Time Input Field
 */
/*
$(() => {
  return // TODO
  const input = $('[data-blogger-time-form]')
  const selectedDate = new Date()

  const createMonthPanel = () => {
    const panel = document.createElement('div')
    panel.className = "blogger-time-panel"

    // create basic elements
    const monthNameElement = document.createElement('span')
    const monthTableElement = document.createElement('div')

    // create buttons
    const btnPrevMonth = document.createElement('a')
    const btnNextMonth = document.createElement('a')

    btnPrevMonth.textContent = "<"
    btnNextMonth.textContent = ">"

    // create head
    const headElement = document.createElement('div')
    headElement.append(btnPrevMonth)
    headElement.append(monthNameElement)
    headElement.append(btnNextMonth)
    headElement.className = "blogger-time-head"

    // construct panel
    panel.append(headElement)
    panel.append(monthTableElement)

    // fill inputs
    const fillInputs = (fillDate) => {
      monthNameElement.textContent = fillDate.getMonth()
    }

    fillInputs(selectedDate)

    return panel
  }

  const parent = input.parent()
  const monthPanel = createMonthPanel();

  parent.append(monthPanel)
})
*/
