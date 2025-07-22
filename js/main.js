
        function showWireframe(wireframeId) {
            // Hide all wireframes
            const wireframes = document.querySelectorAll('.wireframe');
            wireframes.forEach(w => w.classList.remove('active'));
            
            // Remove active class from all buttons
            const buttons = document.querySelectorAll('.wireframe-btn');
            buttons.forEach(b => b.classList.remove('active'));
            
            // Show selected wireframe
            document.getElementById(wireframeId).classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }
 