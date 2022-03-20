class Websocket {
  constructor(cd) {
    this.cd = cd
  }

  init(canvas) {
    const clientId = Math.random().toString(16).slice(2)

    // let selectedText = null;
    //
    // element-clicked, element-selected, element-deselected, element-moved, element-transform, element-created
    // canvas.addEventListener('element-clicked', (e) => {
    //   console.log('element-clicked', e.detail);
    // });
    //
    // canvas.addEventListener('element-selected', (e) => {
    //   console.log('element-selected', e.detail);
    //
    //   if (e.detail.element instanceof CanvasText) {
    //     selectedText = e.detail.element;
    //   }
    // });
    //
    // canvas.addEventListener('element-deselected', (e) => {
    //   console.log('element-deselected', e.detail);
    //   selectedText = null;
    // });
    //
    // canvas.addEventListener('element-moved', (e) => {
    //   console.log('element-moved', e.detail);
    // });
    //
    // canvas.addEventListener('element-transform', (e) => {
    //   console.log('element-transform', e.detail);
    // });
    //
    // canvas.addEventListener('element-created', (e) => {
    //   console.log('element-created', e.detail);
    // });
    //
    // document.querySelector('#text-input').addEventListener('input', (e) => {
    //   if (selectedText) {
    //     selectedText.text = e.target.value;
    //     colladraw.draw()
    //   }
    // });

    const socket = io('http://localhost:8001')

    const drawingId = 1

    socket.on('connect', () => {
      console.info('Websocket connected')

      socket.emit('join-drawing', `drawing-${drawingId}`)

      socket.on('update-drawing', ({ emitterId, ...data }) => {
        if (emitterId !== clientId) {
          this.cd.load(data)
        }
      })

      const send = () => {
        socket.emit('update-drawing', {
          data: this.cd.toJSON(),
          emitterId: clientId,
          room: `drawing-${drawingId}`,
        })
      }

      canvas.addEventListener('element-created', send)
      canvas.addEventListener('element-moved', send)
      canvas.addEventListener('element-transform', send)
    })
  }
}

export default Websocket