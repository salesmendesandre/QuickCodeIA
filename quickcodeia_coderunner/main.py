from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import subprocess
import tempfile
import os
from fastapi.middleware.cors import CORSMiddleware

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # O especifica el origen exacto: ["http://localhost:8080"]
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


class CompileRequest(BaseModel):
    language: str
    code: str

@app.post("/compile")
def compile_code(req: CompileRequest):
    supported_languages = ["python", "c", "verilog", "java"]

    if req.language not in supported_languages:
        raise HTTPException(status_code=400, detail="Unsupported language")

    with tempfile.TemporaryDirectory() as tmpdir:
        exec_cmd = []

        # Python
        if req.language == "python":
            filepath = os.path.join(tmpdir, "script.py")
            with open(filepath, "w") as f:
                f.write(req.code)
            exec_cmd = ["python3", filepath]

        # C
        elif req.language == "c":
            source_path = os.path.join(tmpdir, "program.c")
            binary_path = os.path.join(tmpdir, "program.out")
            with open(source_path, "w") as f:
                f.write(req.code)
            compile = subprocess.run(["gcc", source_path, "-o", binary_path], capture_output=True, text=True)
            if compile.returncode != 0:
                return {"stdout": "", "stderr": compile.stderr, "exit_code": compile.returncode}
            exec_cmd = [binary_path]

        # Verilog
        elif req.language == "verilog":
            source_path = os.path.join(tmpdir, "design.v")
            output_path = os.path.join(tmpdir, "design.out")
            with open(source_path, "w") as f:
                f.write(req.code)
            compile = subprocess.run(["iverilog", "-o", output_path, source_path], capture_output=True, text=True)
            if compile.returncode != 0:
                return {"stdout": "", "stderr": compile.stderr, "exit_code": compile.returncode}
            exec_cmd = ["vvp", output_path]

        # Java
        elif req.language == "java":
            source_path = os.path.join(tmpdir, "Main.java")
            with open(source_path, "w") as f:
                f.write(req.code)
            compile = subprocess.run(["javac", source_path], capture_output=True, text=True)
            if compile.returncode != 0:
                return {"stdout": "", "stderr": compile.stderr, "exit_code": compile.returncode}
            exec_cmd = ["java", "-cp", tmpdir, "Main"]

        # Ejecutar
        try:
            result = subprocess.run(exec_cmd, capture_output=True, text=True, timeout=5)
            return {
                "stdout": result.stdout,
                "stderr": result.stderr,
                "exit_code": result.returncode
            }
        except subprocess.TimeoutExpired:
            return {"stdout": "", "stderr": "Execution timed out", "exit_code": -1}
