#!/usr/bin/env python3
"""
PDF Text Extraction Script
Extracts text from PDF files in pdfs/blogs/ directory
"""

import sys
import os

def extract_text_pypdf(pdf_path):
    """Try using pypdf library"""
    try:
        from pypdf import PdfReader
        reader = PdfReader(pdf_path)
        text = ""
        for page in reader.pages:
            text += page.extract_text() + "\n"
        return text
    except ImportError:
        return None
    except Exception as e:
        print(f"Error with pypdf: {e}", file=sys.stderr)
        return None

def extract_text_pypdf2(pdf_path):
    """Try using PyPDF2 library"""
    try:
        import PyPDF2
        with open(pdf_path, 'rb') as file:
            reader = PyPDF2.PdfReader(file)
            text = ""
            for page in reader.pages:
                text += page.extract_text() + "\n"
        return text
    except ImportError:
        return None
    except Exception as e:
        print(f"Error with PyPDF2: {e}", file=sys.stderr)
        return None

def extract_text_pdfplumber(pdf_path):
    """Try using pdfplumber library"""
    try:
        import pdfplumber
        text = ""
        with pdfplumber.open(pdf_path) as pdf:
            for page in pdf.pages:
                text += page.extract_text() + "\n"
        return text
    except ImportError:
        return None
    except Exception as e:
        print(f"Error with pdfplumber: {e}", file=sys.stderr)
        return None

def extract_text(pdf_path):
    """Extract text from PDF using available library"""
    # Try different libraries in order of preference
    methods = [
        extract_text_pypdf,
        extract_text_pypdf2,
        extract_text_pdfplumber
    ]
    
    for method in methods:
        result = method(pdf_path)
        if result:
            return result
    
    return None

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python3 extract_pdf_text.py <pdf_file>")
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    
    if not os.path.exists(pdf_path):
        print(f"Error: File not found: {pdf_path}", file=sys.stderr)
        sys.exit(1)
    
    text = extract_text(pdf_path)
    
    if text:
        print(text)
    else:
        print("Error: Could not extract text. Please install a PDF library:", file=sys.stderr)
        print("  pip install pypdf", file=sys.stderr)
        print("  or", file=sys.stderr)
        print("  pip install PyPDF2", file=sys.stderr)
        print("  or", file=sys.stderr)
        print("  pip install pdfplumber", file=sys.stderr)
        sys.exit(1)
