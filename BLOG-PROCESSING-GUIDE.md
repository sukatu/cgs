# Blog Processing Guide - Academic Papers to Blog Posts

## Overview
This guide helps you transform academic papers from `pdfs/blogs/` into blog-ready articles for the CGS website.

## Files Found
You have **24 PDF files** in `pdfs/blogs/`:
- ssrn-1665083.pdf
- ssrn-1845429.pdf
- ssrn-2184678.pdf
- ssrn-2658166.pdf (and duplicate)
- ssrn-2858144.pdf
- ssrn-3122230.pdf
- ssrn-3310414.pdf
- ssrn-3505869.pdf
- ssrn-3512771.pdf
- ssrn-3777685.pdf
- ssrn-3790914.pdf
- ssrn-3815642.pdf
- ssrn-4180007.pdf
- ssrn-4180526.pdf
- ssrn-4438349.pdf
- ssrn-4505912.pdf
- ssrn-4510672.pdf
- ssrn-4761109.pdf
- ssrn-4820449.pdf
- ssrn-4937499.pdf
- ssrn-5103133.pdf
- ssrn-5284612.pdf
- ssrn-5309872.pdf
- ssrn-5560978.pdf

## Step-by-Step Process

### Step 1: Extract Text from PDF
Since PDFs cannot be read directly, you need to extract the text first:

**Option A: Using Online Tools**
1. Go to https://www.ilovepdf.com/pdf_to_txt or similar
2. Upload the PDF
3. Download the extracted text

**Option B: Using Adobe Acrobat or Preview (Mac)**
1. Open PDF in Adobe Acrobat or Preview
2. Select all text (Cmd+A / Ctrl+A)
3. Copy and paste into a text editor

**Option C: Using Command Line (if you have pdftotext installed)**
```bash
cd pdfs/blogs
pdftotext filename.pdf filename.txt
```

### Step 2: Fill Out the Template
1. Open `blog-post-template.md`
2. Read through the extracted PDF text
3. Fill in each section following the guidelines:
   - **Metadata Table**: Extract title, authors, publication info
   - **Core Thesis**: Summarize main argument in 2-3 sentences
   - **Methodological Overview**: Explain how research was done
   - **Key Findings**: List 4-5 major insights (paraphrase, no long quotes)
   - **Governance Implications**: Practical actions for boards/CEOs
   - **Our Take**: Add critical commentary with African context

### Step 3: Transform to HTML
1. Open `blog-post-html-template.html`
2. Copy content from your filled template
3. Replace all `[placeholder]` text with your content
4. Save as `blog-[slug].html` (e.g., `blog-board-diversity-africa.html`)

### Step 4: Add to Blog Listing
1. Open `blog.html`
2. Add a new article card in the `updates-grid` section
3. Link to your new blog post HTML file
4. Include appropriate categories and read time

## Content Guidelines

### Writing Style
- ✅ Use simple English for ordinary readers
- ✅ Avoid academic jargon
- ✅ Explain technical terms when necessary
- ✅ Use short paragraphs (3-4 sentences)
- ✅ Break up text with subheadings

### Citation Requirements
- ✅ Always include full metadata table
- ✅ Use proper APA citation format
- ✅ No direct quotes longer than 10 words
- ✅ Paraphrase all concepts
- ✅ Add "Our Take" section for legal protection

### Governance Areas
Common categories to identify:
- Board Effectiveness
- Board Diversity
- ESG (Environmental, Social, Governance)
- Risk Management
- Compliance
- Agency Theory
- Stakeholder Engagement
- Executive Compensation
- Audit & Oversight
- Digital Governance
- State-Owned Enterprises (SOEs)
- SME Governance

### Regional Context
Always add African context in "Our Take" section:
- How does this apply to African companies?
- What are the unique challenges in African markets?
- How can SMEs in Africa benefit from this research?
- What are the limitations for African context?

## Example Workflow

1. **Extract**: `ssrn-4180007.pdf` → Extract text
2. **Analyze**: Read paper, identify governance area, key findings
3. **Template**: Fill `blog-post-template.md`
4. **HTML**: Create `blog-post-ssrn-4180007.html` from template
5. **List**: Add card to `blog.html`
6. **Test**: Open in browser, check formatting

## File Naming Convention

- Blog posts: `blog-[descriptive-slug].html`
- Examples:
  - `blog-board-diversity-performance.html`
  - `blog-esg-african-companies.html`
  - `blog-risk-governance-banking.html`

## Quality Checklist

Before publishing, ensure:
- [ ] All metadata is complete and accurate
- [ ] Core thesis is clear and concise
- [ ] Methodology is explained simply
- [ ] 4-5 key findings are detailed
- [ ] Governance implications are actionable
- [ ] "Our Take" section includes African context
- [ ] No quotes longer than 10 words
- [ ] Proper APA citation included
- [ ] Read time is calculated (~200 words/min)
- [ ] Categories are appropriate
- [ ] HTML file is properly formatted
- [ ] Link works from blog listing page

## Next Steps

1. Start with one PDF at a time
2. Use the templates provided
3. Focus on quality over quantity
4. Test each blog post before moving to the next
5. Consider creating a database/system to manage blog posts in the future

## Need Help?

If you need assistance processing a specific paper:
1. Extract the text from the PDF
2. Share the text or key sections
3. I can help fill out the template and create the HTML
