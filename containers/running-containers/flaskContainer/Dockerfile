# Flask Dockerfile
FROM python:3.11.5-slim

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install Flask and dependencies
RUN pip install -r requirements.txt

# Expose the Flask port
EXPOSE 5000

# Run the Flask application
CMD ["python", "app.py"]