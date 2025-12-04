@extends('layout', ['pageId' => 'recipeManagement', 'grupId' => 'products'])

@section('content')
    <style>
        /* Custom Styles for Recipe Management */
        .recipe-container {
            padding: 20px;
            background-color: #f3f4f6;
            /* Light gray background */
            font-family: 'Inter', sans-serif;
            /* Use a modern font if available, fallback to sans-serif */
        }

        /* Header Section */
        .recipe-header {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-icon {
            background-color: #f59e0b;
            /* Orange/Gold color */
            color: white;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .header-text h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
        }

        .header-text p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .badge-coming-soon {
            background-color: #d946ef;
            /* Purple/Pink */
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .btn-create-recipe {
            background-color: #d97706;
            /* Darker Orange */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }

        .btn-create-recipe:hover {
            background-color: #b45309;
            color: white;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-info h3 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            color: #111827;
            margin-top: 5px;
        }

        .stat-info span {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .icon-blue {
            background-color: #e0f2fe;
            color: #0284c7;
        }

        .icon-green {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .icon-orange {
            background-color: #ffedd5;
            color: #ea580c;
        }

        .icon-purple {
            background-color: #f3e8ff;
            color: #9333ea;
        }

        /* Main Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }

        /* Search & Filter */
        .filter-section {
            background: white;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .search-bar {
            position: relative;
            width: 100%;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background-color: #f9fafb;
            font-size: 14px;
        }

        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .filters-row {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: white;
            color: #374151;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Recipe Cards Grid */
        .recipes-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .recipe-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            border: 1px solid #e5e7eb;
            transition: transform 0.2s;
        }

        .recipe-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .recipe-image-placeholder {
            background-color: #f3f4f6;
            height: 140px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 40px;
            margin-bottom: 15px;
        }

        .recipe-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .recipe-tag {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 500;
        }

        .tag-pastry {
            background-color: #fce7f3;
            color: #db2777;
        }

        .tag-bread {
            background-color: #ffedd5;
            color: #ea580c;
        }

        .recipe-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f3f4f6;
            font-size: 12px;
            color: #6b7280;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .stat-item i {
            margin-right: 4px;
        }

        .recipe-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-size: 13px;
            color: #6b7280;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #22c55e;
            display: inline-block;
            margin-right: 5px;
        }

        .action-icons i {
            margin-left: 10px;
            cursor: pointer;
            color: #4b5563;
        }

        /* Right Sidebar - Categories */
        .categories-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            height: fit-content;
        }

        .categories-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
        }

        .donut-chart-placeholder {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: conic-gradient(#f59e0b 0% 35%,
                    #ec4899 35% 60%,
                    #8b5cf6 60% 90%,
                    #6b7280 90% 100%);
            margin: 0 auto 30px;
            position: relative;
        }

        .donut-inner {
            position: absolute;
            width: 140px;
            height: 140px;
            background: white;
            border-radius: 50%;
            top: 30px;
            left: 30px;
        }

        .legend-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            color: #374151;
        }

        .legend-label {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        /* Modal Styles */
        .modal-xl {
            max-width: 1140px;
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: none;
            padding: 25px 30px 10px;
        }

        .modal-title {
            font-weight: 700;
            font-size: 20px;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-top: 5px;
            margin-left: 38px;
            /* Align with text start */
        }

        .modal-body {
            padding: 20px 30px 30px;
            background-color: #fff;
        }

        .modal-footer {
            border-top: 1px solid #f3f4f6;
            padding: 20px 30px;
            background-color: #f9fafb;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        /* Section Headers */
        .section-header {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .section-header-blue {
            background-color: #eff6ff;
            border: 1px solid #dbeafe;
        }

        .section-header-blue .section-title {
            color: #1d4ed8;
        }

        .section-header-blue .section-desc {
            color: #3b82f6;
        }

        .section-header-yellow {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
        }

        .section-header-yellow .section-title {
            color: #b45309;
        }

        .section-header-yellow .section-desc {
            color: #d97706;
        }

        .section-header-purple {
            background-color: #f3e8ff;
            border: 1px solid #e9d5ff;
        }

        .section-header-purple .section-title {
            color: #7e22ce;
        }

        .section-header-purple .section-desc {
            color: #a855f7;
        }

        .section-title {
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-desc {
            font-size: 12px;
            margin-left: 26px;
        }

        /* Form Elements */
        .form-group label {
            font-weight: 600;
            font-size: 13px;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            font-size: 14px;
            height: auto;
        }

        .form-control:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.1);
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background-color: #f9fafb;
            margin-bottom: 20px;
        }

        .upload-area:hover {
            border-color: #f59e0b;
            background-color: #fffbeb;
        }

        .upload-icon {
            font-size: 24px;
            color: #9ca3af;
            margin-bottom: 10px;
        }

        .upload-text {
            font-size: 13px;
            color: #4b5563;
            font-weight: 500;
        }

        .upload-subtext {
            font-size: 11px;
            color: #9ca3af;
        }

        /* Ingredients Table */
        .ingredients-table {
            width: 100%;
        }

        .ingredients-table th {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
            padding-bottom: 8px;
        }

        .ingredients-table td {
            padding-bottom: 10px;
            padding-right: 10px;
        }

        .btn-add-row {
            width: 100%;
            border: 1px dashed #d1d5db;
            background: white;
            color: #374151;
            padding: 8px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-add-row:hover {
            border-color: #9ca3af;
            background-color: #f9fafb;
        }

        /* Cost Breakdown */
        .cost-breakdown {
            background-color: #ecfdf5;
            border: 1px solid #d1fae5;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .cost-title {
            font-size: 13px;
            font-weight: 600;
            color: #065f46;
            margin-bottom: 15px;
        }

        .cost-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 13px;
            color: #064e3b;
        }

        .cost-row.total {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #a7f3d0;
            font-weight: 700;
            font-size: 16px;
        }

        .cost-input {
            width: 80px;
            text-align: right;
            padding: 4px 8px;
            border: 1px solid #a7f3d0;
            border-radius: 4px;
            background: white;
        }

        /* Instructions */
        .step-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .step-header {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .btn-cancel {
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 500;
            margin-right: 10px;
        }

        .btn-save {
            background-color: #d97706;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 500;
        }

        .btn-save:hover {
            background-color: #b45309;
            color: white;
        }
    </style>

    <div class="recipe-container">
        <!-- Header -->
        <div class="recipe-header">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fa fa-book"></i>
                </div>
                <div class="header-text">
                    <h2>Recipe Management</h2>
                    <p>Create and manage recipes with multi-level bill of materials</p>
                </div>
            </div>
            <div class="header-right">
                <span class="badge-coming-soon">Coming Soon</span>
                <button class="btn-create-recipe" data-toggle="modal" data-target="#createRecipeModal">
                    <i class="fa fa-plus"></i> Create Recipe
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <span>Total Recipes</span>
                    <h3>127</h3>
                </div>
                <div class="stat-icon icon-blue">
                    <i class="fa fa-book"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <span>Active Recipes</span>
                    <h3>98</h3>
                </div>
                <div class="stat-icon icon-green">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <span>Draft Recipes</span>
                    <h3>24</h3>
                </div>
                <div class="stat-icon icon-orange">
                    <i class="fa fa-pencil-square-o"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <span>Average Cost</span>
                    <h3>Rs. 145</h3>
                </div>
                <div class="stat-icon icon-purple">
                    <i class="fa fa-calculator"></i>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Search & Filter -->
                <div class="filter-section">
                    <div class="search-bar">
                        <i class="fa fa-search"></i>
                        <input type="text" placeholder="Search recipes...">
                    </div>
                    <div class="filters-row">
                        <button class="filter-btn">All Categories <i class="fa fa-angle-down"></i></button>
                        <button class="filter-btn">All Status <i class="fa fa-angle-down"></i></button>
                        <button class="filter-btn">All Cost <i class="fa fa-angle-down"></i></button>
                        <button class="filter-btn" style="margin-left: auto;">Sort by: <i
                                class="fa fa-angle-down"></i></button>
                    </div>
                </div>

                <!-- Recipe Grid -->
                <div class="recipes-list">
                    <!-- Card 1 -->
                    <div class="recipe-card">
                        <div class="recipe-image-placeholder">
                            <i class="fa fa-picture-o"></i>
                        </div>
                        <div class="recipe-title">
                            Classic Croissant
                            <span class="recipe-tag tag-pastry">Pastry</span>
                        </div>
                        <div class="recipe-stats">
                            <div class="stat-item">
                                <span><i class="fa fa-cube"></i> 24</span>
                                <span>pcs</span>
                            </div>
                            <div class="stat-item">
                                <span><i class="fa fa-clock-o"></i> 3.5</span>
                                <span>hrs</span>
                            </div>
                            <div class="stat-item">
                                <span>Rs.</span>
                                <span>12.50</span>
                            </div>
                        </div>
                        <div class="recipe-footer">
                            <div><span class="status-dot"></span> v2.3</div>
                            <div class="action-icons">
                                <i class="fa fa-eye"></i>
                                <i class="fa fa-clone"></i>
                                <i class="fa fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="recipe-card">
                        <div class="recipe-image-placeholder">
                            <i class="fa fa-picture-o"></i>
                        </div>
                        <div class="recipe-title">
                            Sourdough Bread
                            <span class="recipe-tag tag-bread">Bread</span>
                        </div>
                        <div class="recipe-stats">
                            <div class="stat-item">
                                <span><i class="fa fa-cube"></i> 2</span>
                                <span>loaves</span>
                            </div>
                            <div class="stat-item">
                                <span><i class="fa fa-clock-o"></i> 24</span>
                                <span>hrs</span>
                            </div>
                            <div class="stat-item">
                                <span>Rs.</span>
                                <span>85.00</span>
                            </div>
                        </div>
                        <div class="recipe-footer">
                            <div><span class="status-dot"></span> v3.1</div>
                            <div class="action-icons">
                                <i class="fa fa-eye"></i>
                                <i class="fa fa-clone"></i>
                                <i class="fa fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <div class="categories-card">
                    <h3 class="categories-title">Recipe Categories</h3>
                    <div class="donut-chart-placeholder">
                        <div class="donut-inner"></div>
                    </div>
                    <div class="legend">
                        <div class="legend-item">
                            <span class="legend-label"><span class="legend-dot" style="background-color: #f59e0b;"></span>
                                Bread</span>
                            <span class="legend-value">35%</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-label"><span class="legend-dot" style="background-color: #ec4899;"></span>
                                Pastry</span>
                            <span class="legend-value">25%</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-label"><span class="legend-dot" style="background-color: #8b5cf6;"></span>
                                Cakes</span>
                            <span class="legend-value">30%</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-label"><span class="legend-dot" style="background-color: #6b7280;"></span>
                                Others</span>
                            <span class="legend-value">10%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Recipe Modal -->
    <div class="modal fade" id="createRecipeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">
                            <span
                                style="background: #f59e0b; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;"><i
                                    class="fa fa-book"></i></span>
                            Create New Recipe
                        </h5>
                        <p class="modal-subtitle">Fill in the details below to create a new recipe with multi-level bill
                            of materials</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column: Basic Info -->
                        <div class="col-lg-6">
                            <div class="section-header section-header-blue">
                                <div class="section-title"><i class="fa fa-cube"></i> Basic Information</div>
                                <div class="section-desc">Enter the fundamental details about your recipe</div>
                            </div>

                            <div class="form-group">
                                <label>Recipe Image</label>
                                <p style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Upload a photo of the
                                    finished product</p>
                                <div class="upload-area">
                                    <div class="upload-icon"><i class="fa fa-cloud-upload"></i></div>
                                    <div class="upload-text">Click to upload or drag and drop</div>
                                    <div class="upload-subtext">PNG, JPG up to 5MB</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Recipe Name *</label>
                                <p style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">What is this recipe
                                    called? (e.g., "Classic Chocolate Chip Cookies")</p>
                                <input type="text" class="form-control" placeholder="Enter recipe name">
                            </div>

                            <div class="form-group">
                                <label>Category *</label>
                                <p style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Select the type of
                                    baked good</p>
                                <select class="form-control">
                                    <option>Bread</option>
                                    <option>Pastry</option>
                                    <option>Cake</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Product Type *</label>
                                <p style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Is this a finished
                                    product or used in other recipes?</p>
                                <select class="form-control">
                                    <option>Finished Product - Ready for sale to customers</option>
                                    <option>Semi-Finished Product - Used in other recipes</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Recipe Status</label>
                                <p style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Active recipes can be
                                    used in production, drafts are for testing</p>
                                <select class="form-control">
                                    <option>Draft - Still in development</option>
                                    <option>Active - Ready for production</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Description (Optional)</label>
                                <p style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Add any notes or
                                    special instructions about this recipe</p>
                                <textarea class="form-control" rows="3"
                                    placeholder="E.g., This recipe produces soft, chewy cookies perfect for retail..."></textarea>
                            </div>
                        </div>

                        <!-- Right Column: Ingredients & Costing -->
                        <div class="col-lg-6">
                            <div class="section-header section-header-yellow">
                                <div class="section-title"><i class="fa fa-cubes"></i> Ingredients & Costing</div>
                                <div class="section-desc">List all ingredients and calculate production costs</div>
                            </div>

                            <div class="form-group">
                                <label>Ingredients *</label>
                                <p style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Add all raw materials
                                    needed for this recipe</p>

                                <table class="ingredients-table" id="ingredientsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 60%">Ingredient Name</th>
                                            <th style="width: 20%">Quantity</th>
                                            <th style="width: 20%">Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="form-control select2">
                                                    <option>Select ingredient...</option>
                                                    <option>Flour</option>
                                                    <option>Sugar</option>
                                                    <option>Butter</option>
                                                </select>
                                            </td>
                                            <td><input type="number" class="form-control" value="500"></td>
                                            <td>
                                                <select class="form-control">
                                                    <option>g</option>
                                                    <option>kg</option>
                                                    <option>ml</option>
                                                    <option>L</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" class="btn-add-row" onclick="addIngredientRow()">
                                    <i class="fa fa-plus"></i> Add Another Ingredient
                                </button>
                            </div>

                            <div class="cost-breakdown">
                                <div class="cost-title">Cost Breakdown (LKR)</div>
                                <p style="font-size: 11px; color: #064e3b; margin-bottom: 15px;">Enter costs in Sri
                                    Lankan Rupees to calculate total recipe cost</p>

                                <div class="cost-row">
                                    <span>Material Costs (Raw ingredients)</span>
                                    <input type="text" class="cost-input" value="0.00" readonly>
                                </div>
                                <div class="cost-row">
                                    <span>Overhead Costs (Utilities, rent)</span>
                                    <input type="text" class="cost-input" value="0.00">
                                </div>
                                <div class="cost-row">
                                    <span>Labor Costs (Staff time)</span>
                                    <input type="text" class="cost-input" value="0.00">
                                </div>
                                <div class="cost-row total">
                                    <span>Total Cost Per Batch</span>
                                    <span style="font-size: 18px; color: #059669;">Rs. 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Section: Instructions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="section-header section-header-purple">
                                <div class="section-title"><i class="fa fa-list-ol"></i> Preparation Instructions</div>
                                <div class="section-desc">Step-by-step instructions for making this recipe</div>
                            </div>

                            <div id="stepsContainer">
                                <div class="step-card">
                                    <div class="step-header">Step 1</div>
                                    <textarea class="form-control" rows="2"
                                        placeholder="E.g., Preheat oven to 180°C and prepare baking trays with parchment paper..."></textarea>
                                </div>
                            </div>

                            <button type="button" class="btn-add-row" onclick="addStep()">
                                <i class="fa fa-plus"></i> Add Another Step
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-dismiss="modal"><i class="fa fa-times"></i>
                        Cancel</button>
                    <button type="button" class="btn-save"><i class="fa fa-plus"></i> Create Recipe</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addIngredientRow() {
            const row = `
                <tr>
                    <td>
                        <select class="form-control select2">
                            <option>Select ingredient...</option>
                            <option>Flour</option>
                            <option>Sugar</option>
                            <option>Butter</option>
                        </select>
                    </td>
                    <td><input type="number" class="form-control" placeholder="0"></td>
                    <td>
                        <select class="form-control">
                            <option>g</option>
                            <option>kg</option>
                            <option>ml</option>
                            <option>L</option>
                        </select>
                    </td>
                </tr>
            `;
            $('#ingredientsTable tbody').append(row);
        }

        function addStep() {
            const stepCount = $('#stepsContainer .step-card').length + 1;
            const step = `
                <div class="step-card">
                    <div class="step-header">Step ${stepCount}</div>
                    <textarea class="form-control" rows="2" placeholder="Describe this step..."></textarea>
                </div>
            `;
            $('#stepsContainer').append(step);
        }
    </script>
@endsection