export default (to, from, next) => {
  if (localStorage.getItem('baikAnggota') != null && localStorage.getItem('baikAnggota').length > 0) {
    next()
  } else {
    localStorage.removeItem('baikAnggota')
    next('/login')
  }
}
