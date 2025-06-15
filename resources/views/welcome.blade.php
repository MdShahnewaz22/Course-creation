<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* styles unchanged (same as yours) */
        body {
            background-color: #dde1ea;
            color: #fff;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background-color: #1e293b;
            padding: 20px;
            border-radius: 10px;
        }

        h2 {
            margin-bottom: 10px;
        }

        .input-group {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }

        .input-group input {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #334155;
            color: white;
        }

        .module {
            margin-top: 20px;
            padding: 15px;
            background-color: #334155;
            border-radius: 8px;
        }

        .content {
            background-color: #475569;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .button {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .remove-button {
            background-color: #ef4444;
            margin-left: 10px;
        }

        .actions {
            margin-top: 20px;
        }

        .actions button {
            margin-right: 10px;
        }

        select {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #334155;
            color: white;
            width: 100%;
            margin-top: 10px;
        }

        input[type="text"] {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Create a Course</h2>
        <form id="courseForm" enctype="multipart/form-data">
            <div class="input-group">
                <input type="text" name="title" placeholder="Course Title" required>
                <input type="text" name="description" placeholder="Description" required>
                <input type="text" name="category" placeholder="Category" required>
            </div>
            <div class="input-group">
                <input type="text" name="level" placeholder="Level" required>
                <input type="text" name="price" placeholder="Course Price" required>
            </div>


            <div id="modules"></div>
            <button style="margin-top: 10px" type="button" class="button" onclick="addModule()">Add Module +</button>
            <div class="actions">
                <button type="submit" class="button">Save</button>
                <button type="reset" class="button remove-button">Cancel</button>
            </div>
        </form>
    </div>

    <script>
        let moduleIndex = 0;

        document.getElementById('courseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            //image
            const formData = new FormData(this);

            const title = this.querySelector('[name="title"]').value;
            const description = this.querySelector('[name="description"]').value;
            const category = this.querySelector('[name="category"]').value;
            const level = this.querySelector('[name="level"]').value;
            const price = this.querySelector('[name="price"]').value;

            const modules = [];
            document.querySelectorAll('.module').forEach((moduleDiv, moduleIdx) => {
                const moduleTitle = moduleDiv.querySelector(`input[name="modules[${moduleIdx}][title]"]`)
                    .value;

                const contents = [];
                moduleDiv.querySelectorAll('.content').forEach((contentDiv, contentIdx) => {
                    const contentTitle = contentDiv.querySelector(
                        `[name="modules[${moduleIdx}][contents][${contentIdx}][title]"]`).value;

                    const imageInput = contentDiv.querySelector(
                        `[name="modules[${moduleIdx}][contents][${contentIdx}][image]"]`);
                    const imageFile = imageInput.files[0];


                    const videoType = contentDiv.querySelector(
                            `[name="modules[${moduleIdx}][contents][${contentIdx}][video_type]"]`)
                        .value;
                    const url = contentDiv.querySelector(
                        `[name="modules[${moduleIdx}][contents][${contentIdx}][url]"]`).value;
                    const length = contentDiv.querySelector(
                        `[name="modules[${moduleIdx}][contents][${contentIdx}][length]"]`).value;



                    contents.push({
                        title: contentTitle,
                        image: imageInput,
                        video_type: videoType,
                        url,
                        length
                    });
                });

                modules.push({
                    title: moduleTitle,
                    contents
                });
            });

            fetch('/courses', {
                    method: 'POST',
                    body: formData, // এখানে JSON.stringify() নয়, সরাসরি formData পাঠাতে হবে
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(res => res.json())
                .then(data => {
                    alert('Your course has been saved successfully.');
                    console.log(data);
                    this.reset();
                    document.getElementById('modules').innerHTML = '';
                    moduleIndex = 0;
                })
                .catch(err => {
                    alert('Error: ' + err.message);
                    console.error(err)
                });
        });

        function addModule() {
            const modulesDiv = document.getElementById('modules');
            const moduleDiv = document.createElement('div');
            moduleDiv.className = 'module';
            const currentModule = moduleIndex++;

            moduleDiv.innerHTML = `
            <p>Module Create </p>
            <div>
              <input type="text" name="modules[${currentModule}][title]" placeholder="Module Title"  style="width: 50%; padding: 10px; margin-top: 10px;   margin-bottom: 10px; border-radius: 5px; background-color: #475569; color: white; border: none;">
          </div>

            <div class="contents" id="module-${currentModule}-contents"></div>
            <button style="margin-top: 10px" class="button" type="button" onclick="addContent(${currentModule})">Add Content +</button>
            <button style="margin-top: 10px" class="button remove-button" type="button" onclick="this.parentElement.remove()">Remove Module</button>
        `;

            modulesDiv.appendChild(moduleDiv);
        }

        function addContent(moduleId) {
            const contentsDiv = document.getElementById(`module-${moduleId}-contents`);
            const contentIndex = contentsDiv.children.length;

            const contentDiv = document.createElement('div');
            contentDiv.className = 'content';
            contentDiv.innerHTML = `
            <h3> Content Create</h3>
                 <div style="display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px;">

                        <!-- Text Input Block -->
                        <div>
                            <p style="margin: 0;">Content Title</p>
                            <input 
                                type="text" 
                                name="modules[${moduleId}][contents][${contentIndex}][title]" 
                                placeholder="Content Title"
                                style="width: 450px; padding: 10px; margin-top: 5px; border-radius: 5px; background-color: #f7d9cd; border: none;">
                        </div>

                        <!-- File Input Block -->
                        <div>
                            <p style="margin: 0;">Content Image</p>
                            <input 
                                type="file" 
                                name="modules[${moduleId}][contents][${contentIndex}][image]" 
                                accept="image/*"
                                onchange="previewImage(event, 'preview-${moduleId}-${contentIndex}')"
                                style="width: 450px; padding: 10px; margin-top: 5px; border-radius: 5px; background-color: #f7d9cd; border: none;">
                            
                            <img 
                                id="preview-${moduleId}-${contentIndex}" 
                                src="" 
                                alt="Image Preview" 
                                style="margin-top: 10px; max-width: 200px; display: none; border-radius: 10px;">
                        </div>
                    </div>


            <div>
                <p style="margin: 0;">Video Source Type</p>
                <div style="width: 960px;">
                <select name="modules[${moduleId}][contents][${contentIndex}][video_type]" >
                        <option value="">Choose...</option>
                        <option value="youtube">YouTube</option>
                        <option value="vimeo">Vimeo</option>
                        <option value="upload">Direct Upload</option>
                </select>    
                 </div>
            </div>

            <div style="display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px; margin-top: 20px;">

                    <!-- Video URL -->
                    <div>
                        <p style="margin: 0;">Video URL</p>
                        <input 
                            type="text" 
                            name="modules[${moduleId}][contents][${contentIndex}][url]" 
                            placeholder="Video URL"
                            style="width: 450px; padding: 10px; margin-top: 5px; border-radius: 5px; background-color: #f7d9cd; border: none;">
                    </div>

                    <!-- Video Length -->
                    <div>
                        <p style="margin: 0;">Video Length</p>
                        <input 
                            type="text" 
                            name="modules[${moduleId}][contents][${contentIndex}][length]" 
                            placeholder="HH:MM:SS"
                            style="width: 450px; padding: 10px; margin-top: 5px; border-radius: 5px; background-color: #f7d9cd; border: none;">
                    </div>

                </div>

            <button class="button remove-button" type="button" onclick="this.parentElement.remove()">Remove Content</button>
            `;

            contentsDiv.appendChild(contentDiv);
        }

        function previewImage(event, previewId) {
            const input = event.target;
            const preview = document.getElementById(previewId);

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }
    </script>
</body>

</html>