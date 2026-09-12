#!/usr/bin/env node
import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';

const root = path.resolve(process.argv[2] ?? '.');
const sources = ['TODO.md', 'TODO_ARCHIVE.md'];
const output = path.join(root, '.executionkit', 'task-projection.json');
const markerState = {
  ' ': 'PLANNED', '~': 'IMPLEMENTED', '!': 'BLOCKED', B: 'BLOCKED', b: 'BLOCKED',
  R: 'REVIEW_REQUIRED', r: 'REVIEW_REQUIRED', x: 'ACCEPTED', X: 'ACCEPTED'
};
const k00Dependencies = {
  'K00-001': [],
  'K00-002': ['K00-001'],
  'K00-003': ['K00-002'],
  'K00-004': ['K00-003'],
  'K00-005': ['K00-004'],
  'K00-006': ['K00-005'],
  'K00-007': ['K00-006'],
  'K00-013': ['K00-007'],
  'K00-008': ['K00-013'],
  'K00-009': ['K00-008'],
  'K00-010': ['K00-009'],
  'K00-011': ['K00-010'],
  'K00-012': ['K00-011'],
  'K00-014': ['K00-012'],
  'K00-015': ['K00-014'],
  'K00-016': ['K00-015'],
  'K00-017': ['K00-016'],
  'K00-018': ['K00-017'],
  'K00-019': ['K00-018'],
  'K00-GATE': ['K00-019']
};
const k00Tags = {
  'K00-001': ['tooling-bootstrap','executionkit','installer','mcp','state'],
  'K00-002': ['project-study','planning','architecture','discovery'],
  'K00-003': ['executionkit','runtime','installer'],
  'K00-004': ['agent','governance','project-study'],
  'K00-005': ['systems','premium','premium-experience','design-system','component-system'],
  'K00-006': ['execution'],
  'K00-007': ['agent'],
  'K00-008': ['content','localization'],
  'K00-009': ['seo','seo-os','public-web'],
  'K00-010': ['audit','premium','quality'],
  'K00-011': ['test','quality-evidence'],
  'K00-012': ['launch','release'],
  'K00-013': ['state','continuity'],
  'K00-014': ['agent','routing'],
  'K00-015': ['tooling','mcp','agent'],
  'K00-016': ['execution','task-projection'],
  'K00-017': ['validation','readiness'],
  'K00-018': ['docs','operator'],
  'K00-019': ['phase-audit','deep-audit','honest-audit','full-audit','integration','premium','seo'],
  'K00-GATE': ['activation','integration-gate']
};

function hashFile(full) {
  return crypto.createHash('sha256').update(fs.readFileSync(full)).digest('hex');
}
function phaseNumber(phase) {
  const m = /^P(\d{2})$/.exec(phase);
  return m ? Number(m[1]) : null;
}
function previousGate(phase) {
  const n = phaseNumber(phase);
  if (n === null || n === 0) return null;
  if (n === 1) return 'K00-GATE';
  return `P${String(n - 1).padStart(2,'0')}-GATE`;
}
function inferTags(id, title, phase) {
  if (k00Tags[id]) return k00Tags[id];
  const s = `${id} ${title}`.toLowerCase();
  const tags = new Set();
  if (/laravel|backend|api|database|redis|queue|reverb/.test(s)) tags.add('backend');
  if (/flutter|drift|mobile|device|offline|sync/.test(s)) { tags.add('flutter'); tags.add('offline'); }
  if (/ui|ux|surface|dashboard|responsive|rtl|accessib|design|component|screen|pos shell|waiter|barista/.test(s)) { tags.add('ui'); tags.add('premium'); }
  if (/platform|saas|subscription|entitlement|tenant|commercial plan/.test(s)) { tags.add('platform'); tags.add('saas'); }
  if (/menuza|payment|fiscal|eta|router|mikrotik|wifi|integration|webhook/.test(s)) tags.add('integration');
  if (/security|privacy|authorization|permission|secret|threat/.test(s)) tags.add('security');
  if (/audit/.test(id.toLowerCase()) || /audit/.test(s)) tags.add('audit');
  if (/test|verify|validation|regression/.test(s)) tags.add('test');
  if (/launch|release|rollout|rollback|pilot|production/.test(s)) tags.add('launch');
  if (/content|copy|translation|arabic|english|localization/.test(s)) tags.add('content');
  if (/seo|public website|commercial website|organic/.test(s)) tags.add('seo');
  if (phase === 'P00') tags.add('planning');
  return [...tags];
}
function parseEvidence(text, state, phase) {
  const m = text.match(/\*\*Evidence:\*\*\s*([^]*?)(?=\s+\*\*|$)/i);
  if (m?.[1]?.trim()) return m[1].trim();
  if (state === 'ACCEPTED' && phase === 'P00') return 'Pre-adoption accepted planning evidence preserved in Bunova git history and linked native planning authorities.';
  return 'PENDING';
}

const tasks = [];
const sourceEntries = [];
let order = 0;
for (const rel of sources) {
  const full = path.join(root, rel);
  if (!fs.existsSync(full)) continue;
  const text = fs.readFileSync(full, 'utf8');
  sourceEntries.push({ path: rel, sha256: hashFile(full) });
  let phase = '';
  text.split(/\r?\n/).forEach((line, index) => {
    const h = line.match(/^#\s+(P\d{2}|K00)\b/);
    if (h) phase = h[1];
    const m = line.match(/^\s*-\s*\[([ xX~!RrBb])\]\s+`?([A-Z][A-Z0-9._]*-[A-Z0-9._-]+)`?\s+(.+)$/);
    if (!m) return;
    const [, marker, id, rest] = m;
    const title = rest.split(/\s+\*\*/)[0].trim();
    tasks.push({
      id, title, state: markerState[marker] ?? 'PLANNED', phase: phase || id.split('-')[0], priority: phase === 'K00' ? 'P0' : 'P1',
      dependencies: [], action: '', auditBoundary: '', contract: phase === 'K00' ? 'docs/executionkit/INTEGRATION_PLAN.md' : 'TODO.md',
      verification: rest, evidence: parseEvidence(rest, markerState[marker], phase), blocker: markerState[marker] === 'BLOCKED' ? 'See canonical TODO task text and linked evidence.' : '',
      blockerActionable: false, unblockAction: '', reason: '', tags: inferTags(id, title, phase), risk: '', objective: title,
      sourcePath: rel, startLine: index + 1, order: order++, isArchive: rel !== 'TODO.md'
    });
  });
}
const byId = new Map(tasks.map(t => [t.id, t]));
for (const task of tasks) {
  if (task.isArchive) continue;
  if (task.phase === 'K00') {
    task.dependencies = (k00Dependencies[task.id] ?? []).filter(id => byId.has(id));
    if (task.id === 'K00-019') { task.action = 'audit'; task.auditBoundary = 'phase'; }
    continue;
  }
  if (task.phase === 'P00') continue;
  const prev = previousGate(task.phase);
  const samePhase = tasks.filter(t => !t.isArchive && t.phase === task.phase);
  if (task.id.endsWith('-AUDIT')) {
    task.action = 'audit'; task.auditBoundary = 'phase';
    task.dependencies = samePhase.filter(t => t.id !== task.id && !t.id.endsWith('-GATE')).map(t => t.id);
  } else if (task.id.endsWith('-GATE')) {
    const audit = samePhase.find(t => t.id.endsWith('-AUDIT'));
    task.dependencies = audit ? [audit.id] : samePhase.filter(t => t.id !== task.id).map(t => t.id);
  } else if (prev && byId.has(prev)) {
    task.dependencies = [prev];
  }
}

fs.mkdirSync(path.dirname(output), { recursive: true });
fs.writeFileSync(output, JSON.stringify({ schemaVersion: '2.0.0', generatedAt: new Date().toISOString(), sources: sourceEntries, tasks }, null, 2) + '\n');
console.log(`Bunova task projection: ${tasks.length} tasks from ${sourceEntries.map(s => s.path).join(', ')}`);
