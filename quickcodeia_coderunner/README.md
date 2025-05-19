# QuickCodeIA CodeRunner

This service provides a secure, containerized backend to **compile and execute student code** in multiple programming languages for the [QuickCode IA](https://github.com/salesmendesandre/QuickCodeIA) plugin in Moodle.

### ✅ Supported languages:

- Python
- C
- Verilog (via Icarus Verilog)
- Java (via OpenJDK)

---

## 📁 Project Structure

```
quickcodeia_coderunner/
├── main.py       # FastAPI backend with /compile endpoint
├── Dockerfile    # Docker image with compiler/runtime tools
```

---

## 🚀 How it works

The FastAPI service exposes a single endpoint:

### `POST /compile`

**Request example:**

```json
{
  "language": "python", // or "c", "verilog", "java"
  "code": "print('Hello')"
}
```

**Response example:**

```json
{
  "stdout": "Hello\n",
  "stderr": "",
  "exit_code": 0
}
```

---

## 🐳 Docker usage

### Build the image:

```bash
docker build -t quickcodeia_coderunner .
```

### Run the container:

```bash
docker run -p 8000:8000 quickcodeia_coderunner
```

Once running, the server will be available at:

```
http://localhost:8000/compile
```

---

## 🔧 Installed tools (inside Docker)

- `python3` for Python
- `gcc` for C
- `openjdk-17-jdk` for Java
- `iverilog` and `vvp` for Verilog

---

## 🛡️ Security & Limitations

- Code is executed in a temporary sandboxed directory.
- Maximum execution timeout: **5 seconds**
- This setup is intended for **educational/demo purposes**.
  
---

## 📄 License

This project is distributed under the terms of the **GNU General Public License v3**.  
See [LICENSE](https://www.gnu.org/licenses/gpl-3.0.html) for full details.
