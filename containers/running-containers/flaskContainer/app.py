from flask import Flask, jsonify, abort
import os
import json

app = Flask(__name__)

# Define the directory where JSON data files are stored
DATA_DIR = 'data'

@app.route('/output/<int:id>', methods=['GET'])
def output(id):
    # Construct the filename based on the ID
    filename = os.path.join(DATA_DIR, f"{id}.json")
    
    # Check if the file exists
    if not os.path.exists(filename):
        # If the file does not exist, return a 404 error
        abort(404, description="Resource not found")
    
    # Read the JSON data from the file
    with open(filename, 'r') as file:
        data = json.load(file)
    
    # Return the data as JSON
    return jsonify(data)

if __name__ == '__main__':
    app.run(port=8050, debug=True)