// Clean contextual detection tracking mapping logic configuration
// Uses relative path - works on both localhost (htdocs) and production without changes
const API_BASE_URL = '../backend/api';

const PortfolioAPI = {
    async login(username, password) {
        try {
            const response = await fetch(`${API_BASE_URL}/login.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Server unreachable.' };
        }
    },

    async logout() {
        try {
            const response = await fetch(`${API_BASE_URL}/logout.php`, { method: 'POST' });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Logout pipeline error.' };
        }
    },

    async checkAuth() {
        try {
            const response = await fetch(`${API_BASE_URL}/check-auth.php`);
            return await response.json();
        } catch (error) {
            return { authenticated: false };
        }
    },

    // --- Dynamic Portfolio Management Methods ---
    async fetchProjects() {
        try {
            const response = await fetch(`${API_BASE_URL}/projects.php`);
            return await response.json();
        } catch (error) {
            console.error(error);
            return [];
        }
    },

    async addProject(projectData) {
        try {
            const response = await fetch(`${API_BASE_URL}/projects.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(projectData)
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Failed to create entry.' };
        }
    },

    async updateProject(projectData) {
        try {
            const response = await fetch(`${API_BASE_URL}/projects.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(projectData)
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Failed to save updates.' };
        }
    },

    async deleteProject(id) {
        try {
            const response = await fetch(`${API_BASE_URL}/projects.php?id=${id}`, {
                method: 'DELETE'
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Failed to remove resource.' };
        }
    },

    // --- Profile Management Methods ---
    async fetchProfile() {
        try {
            const response = await fetch(`${API_BASE_URL}/profile.php`);
            return await response.json();
        } catch (error) {
            console.error(error);
            return null;
        }
    },

    async updateProfile(profileData) {
        try {
            const response = await fetch(`${API_BASE_URL}/profile.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(profileData)
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Failed to update profile.' };
        }
    },

    // --- Blog Management Methods ---
    async fetchBlogs() {
        try {
            const response = await fetch(`${API_BASE_URL}/blogs.php`);
            return await response.json();
        } catch (error) {
            console.error(error);
            return [];
        }
    },

    async addBlog(blogData) {
        try {
            const response = await fetch(`${API_BASE_URL}/blogs.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(blogData)
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Failed to create blog post.' };
        }
    },

    async updateBlog(blogData) {
        try {
            const response = await fetch(`${API_BASE_URL}/blogs.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(blogData)
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Failed to update blog post.' };
        }
    },

    async deleteBlog(id) {
        try {
            const response = await fetch(`${API_BASE_URL}/blogs.php?id=${id}`, {
                method: 'DELETE'
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Failed to delete blog post.' };
        }
    },

    async uploadImage(file) {
        try {
            const formData = new FormData();
            formData.append('image', file);
            
            const response = await fetch(`${API_BASE_URL}/upload.php`, {
                method: 'POST',
                body: formData
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Failed to upload image.' };
        }
    },

    async changePassword(currentPassword, newPassword) {
        try {
            const response = await fetch(`${API_BASE_URL}/change-password.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ currentPassword, newPassword })
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Failed to change password.' };
        }
    }
};
