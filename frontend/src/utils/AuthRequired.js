export default (to, from, next) => {
  if (localStorage.getItem('kisMobileApp') != null && localStorage.getItem('kisMobileApp').length > 0) {
    next()
  } else {
    localStorage.removeItem('kisMobileApp')
    next('/login')
  }
}
