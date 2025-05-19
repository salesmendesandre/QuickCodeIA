# QuickCodeIA Solver

Frontend application built with **Vue 3 + Vite**, designed to serve as the interactive coding interface for the QuickCode IA system.

---

## 🚀 Features

- ✨ Built with Vue 3 + Vite
- 🧠 Integrated with AI hints and console help
- 🖥️ Syntax highlighting and code editor
- 📡 Communicates with a backend using `postMessage`
- 🔧 Executes code via external APIs

---

## 📦 Project Structure

```
quickcodeia_solver/
├── public/            # Static assets
├── src/
│   ├── assets/        # Icons, styles, etc.
│   ├── components/    # Vue components (editor, console, hints...)
│   ├── views/         # Main layout/view
│   ├── App.vue
│   └── main.js
├── index.html         # Entry point
├── vite.config.js     # Vite config
```

---

## 🧪 Development

Install dependencies:

```bash
npm install
```

Start development server:

```bash
npm run dev
```

Access the app at: [http://localhost:5173](http://localhost:5173)

---

## 🏗️ Production Build

```bash
npm run build
```

The output will be in the `dist/` directory.

To ensure assets use relative paths (useful when embedding), set this in `vite.config.js`:

```js
export default defineConfig({
  base: './',
});
```

---

## 🛠️ Tech Stack

- [Vue 3](https://vuejs.org/)
- [Vite](https://vitejs.dev/)
- [Monaco Editor](https://microsoft.github.io/monaco-editor/) or [CodeMirror]
- [highlight.js](https://highlightjs.org/)
- [Material Design Icons](https://cdn.jsdelivr.net/npm/@mdi/font/)

---

## 📄 License

Distributed under the terms of the GNU GPL v3.  
See [LICENSE](https://www.gnu.org/licenses/gpl-3.0.html) for more details.