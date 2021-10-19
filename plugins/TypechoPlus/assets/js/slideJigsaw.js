const slideJigsaw = {
    init(options, onSuccess, onFail) {
        this.slideJigsaw = document.querySelector('#slideJigsaw')

        // dom
        this.panel = this.slideJigsaw.querySelector('.panel')
        this.jigsaw = this.slideJigsaw.querySelector('.jigsaw')
        this.loading = this.slideJigsaw.querySelector('.sloading')
        this.refresh = this.slideJigsaw.querySelector('.refresh')
        this.indicator = this.slideJigsaw.querySelector('.indicator')
        this.slider = this.slideJigsaw.querySelector('.slider')
        this.tips = this.slideJigsaw.querySelector('.tips')
        this.img = document.createElement('img')

        // size
        this.w = this.panel.width = this.panel.offsetWidth
        this.h = this.panel.height = this.panel.offsetHeight
        this.l = 40
        this.r = 10
        this.x = 0
        this.y = 0

        // options
        Object.assign(this, options)
        this.onSuccess = onSuccess
        this.onFail = onFail

        // canvas
        this.panel_ctx = this.panel.getContext('2d')
        this.jigsaw_ctx = this.jigsaw.getContext('2d')

        // reset
        this.resetDraw()
    },

    resetDraw() {
        const _w = this.l + this.r * 2

        this.jigsaw.width = this.w
        this.indicator.style.width = this.numToPx(0)
        this.jigsaw.style.left = this.numToPx(0)
        this.slider.style.left = this.numToPx(0)

        this.x = Math.floor(Math.random() * (this.w - _w * 2) + _w)
        this.y = Math.floor(Math.random() * (this.h - _w) + this.r * 2)

        this.panel_ctx.clearRect(0, 0, this.w, this.h)
        this.jigsaw_ctx.clearRect(0, 0, this.w, this.h)

        // img src
        this.setImgSrc()

        // draw
        this.drawPath(this.panel_ctx, 'fill')
        this.drawPath(this.jigsaw_ctx, 'clip')
    },

    setImgSrc() {
        this.showLoading(true)
        this.showTips(true)
        this.setState('')

        this.img.crossOrigin = "anonymous"
        this.img.src = `https://picsum.photos/id/${this.randomInt(1000)}/${this.w}/${this.h}`
        this.img.onerror = () => this.resetDraw()
        this.img.onload = () => {
            this.panel_ctx.drawImage(this.img, 0, 0, this.w, this.h)
            this.jigsaw_ctx.drawImage(this.img, 0, 0, this.w, this.h)

            const _w = this.l + this.r * 2
            const _y = this.y - this.r * 2
            const imageData = this.jigsaw_ctx.getImageData(this.x, _y, _w, _w)

            this.jigsaw.width = _w // do not use style width
            this.jigsaw_ctx.putImageData(imageData, 0, _y)

            // hide loading
            this.showLoading(false)

            // events
            this.bindEvents()
        }
    },

    drawPath(ctx, operation) {
        const {x, y, l, r} = this
        const PI = Math.PI

        ctx.beginPath()
        ctx.moveTo(x, y)

        // draw top circle (clockwise from bottom 0.5 to 2.5)
        ctx.arc(x + l / 2, y - r + 2.5, r, (0.5 + 0.25) * PI, (2.5 - 0.25) * PI)
        ctx.lineTo(x + l, y)

        // draw left circle (clockwise from left 1 to 3)
        ctx.arc(x + l + r - 2.5, y + l / 2, r, (1 + 0.25) * PI, (3 - 0.25) * PI)

        // draw rect
        ctx.lineTo(x + l, y + l)
        ctx.lineTo(x, y + l)

        // draw left concave semicircle(anti-clockwise from left 1 to 3)
        ctx.arc(x + r - 2.5, y + l / 2, r, (3 - 0.25) * PI, (1 + 0.25) * PI, true)
        ctx.lineTo(x, y)
        ctx.lineWidth = 2

        ctx.fillStyle = 'rgba(255, 255, 255, 0.5)' // fill style
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.5)' // border style
        ctx.stroke()

        ctx.globalCompositeOperation = 'destination-over'
        ctx[operation === 'fill' ? 'fill' : 'clip']() //fill or clip
    },

    bindEvents() {
        let oX = 0
        let oY = 0
        let canMove = false

        const dragStart = (e) => {
            oX = e.x || e.touches[0].clientX
            oY = e.y || e.touches[0].clientY
            canMove = true

            this.showTips(false)
        }

        const dragMove = (e) => {
            if (!canMove) return
            e.preventDefault()

            const mX = (e.x || e.touches[0].clientX) - oX
            const jigsaw_x = mX < 0 ? Math.max(0, mX) : Math.min(this.w - this.l - this.r * 2, mX)
            const slider_x = mX < 0 ? Math.max(0, mX) : Math.min(this.w - this.slider.offsetWidth, mX)

            this.jigsaw.style.left = this.numToPx(jigsaw_x)
            this.slider.style.left = this.numToPx(slider_x)
            this.indicator.style.width = this.numToPx(slider_x)
        }

        const dragEnd = (e) => {
            if (!canMove) return
            canMove = false

            const left = parseInt(this.jigsaw.style.left)
            if (Math.abs(left - this.x) < this.r / 4) {
                this.setState('onSuccessess')
                typeof this.onSuccess === 'function' && this.onSuccess()
            } else {
                this.setState('onFail')
                typeof this.onFail === 'function' && this.onFail()

                setTimeout(() => {
                    this.resetDraw()
                }, 1000)
            }

            // touch
            this.slider.removeEventListener('touchstart', dragStart)
            document.removeEventListener('touchmove', dragMove)
            document.removeEventListener('touchend', dragEnd)
            // mouse event
            this.slider.removeEventListener('mousedown', dragStart)
            document.removeEventListener('mousemove', dragMove)
            document.removeEventListener('mouseup', dragEnd)
        }

        // touch
        this.slider.addEventListener('touchstart', dragStart)
        document.addEventListener('touchmove', dragMove)
        document.addEventListener('touchend', dragEnd)
        // mouse event
        this.slider.addEventListener('mousedown', dragStart)
        document.addEventListener('mousemove', dragMove)
        document.addEventListener('mouseup', dragEnd)

        // refresh
        this.refresh.addEventListener('click', () => this.resetDraw())
    },

    numToPx(num) {
        return num + 'px'
    },

    randomInt(num) {
        return Math.floor(Math.random() * (num || 1000) + 1)
    },

    showLoading(show) {
        this.loading.style.display = show ? 'block' : 'none'
        // this.slideJigsaw.style.pointerEvents = show ? 'none' : ''
    },

    showTips(show) {
        this.tips.style.display = show ? 'block' : 'none'
    },

    setState(state) {
        this.slider.className = 'slider ' + state
    }
}