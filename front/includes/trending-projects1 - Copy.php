<?php
if (!function_exists('slugify')) {
    function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}

// ===============================
// Fetch Section Heading
// ===============================
$section = [
    'title' => 'Prominent Projects',
    'subtitle' => 'Hand-picked'
];
$sectionResult = $conn->query("SELECT * FROM trending_projects_section LIMIT 1");
if ($sectionResult && $sectionResult->num_rows > 0) {
    $section = $sectionResult->fetch_assoc();
}

// ===============================
// Detect whether trending_projects has a real `slug` column
// ===============================
$hasSlugColumn = false;
$colCheck = $conn->query("SHOW COLUMNS FROM trending_projects LIKE 'slug'");
if ($colCheck && $colCheck->num_rows > 0) {
    $hasSlugColumn = true;
}

// ===============================
// Fetch Trending Projects
// ===============================
$projects = [];
$sql = "SELECT *
        FROM trending_projects
        WHERE status='Active'
        ORDER BY project_order DESC LIMIT 3";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}
?>
<section class="section pt-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="eyebrow">
                    Most-viewed
                </span>
                <h2 class="mt-1 mb-0" style="font-size:1.7rem;">
                    Prominent projects to explore
                </h2>
            </div>
            <a href="#" class="btn-outline-ink d-none d-md-inline-block">
                View all projects
            </a>
        </div>
        <div class="row g-4">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project):
                    $image = !empty($project['image'])
                        ? "backend/uploads/trending-projects/" . $project['image']
                        : "assets/images/no-image.jpg";

                    // Use the real slug column if it exists, otherwise derive one from the project name
                    $slug = ($hasSlugColumn && !empty($project['slug']))
                        ? $project['slug']
                        : slugify($project['project_name']);
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="project-card">
                            <a href="property-listing/<?php echo htmlspecialchars($slug); ?>/">
                            <?php
                                $imagePath = "../backend/uploads/trending-projects/" . $project['image'];
                            ?>
                            <div class="thumb"
                                 style="background-image:url('<?php echo htmlspecialchars($imagePath); ?>');">
                            </div>
                            <div class="body">
                                <h3>
                                    <?php echo htmlspecialchars($project['project_name']); ?>
                                </h3>
                                <div class="locality">
                                    <i class="bi bi-geo-alt"></i>
                                    <?php echo htmlspecialchars($project['address']); ?>
                                </div>
                                <div class="price">
                                    <?php echo htmlspecialchars($project['price']); ?>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning">
                        No trending projects found.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>