// Format date as YYYY-MM-DD
export function formatDate(date: Date): string {
    const isoString = date.toISOString()
    const parts = isoString.split('T')
    return parts[0] || ''
}

// Format date for display (e.g., "Monday, December 15")
export function formatDateLabel(date: Date): string {
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric'
    })
}

// Get week key for grouping (e.g., "Last week", "2nd Week of December")
export function getWeekKey(date: Date): string {
    const today = new Date()
    const diffTime = today.getTime() - date.getTime()
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays >= 7 && diffDays < 14) {
        return 'Last week'
    }

    const weekOfMonth = Math.ceil(date.getDate() / 7)
    const monthName = date.toLocaleDateString('en-US', { month: 'long' })
    
    const ordinal = ['', '1st', '2nd', '3rd', '4th', '5th'][weekOfMonth] || `${weekOfMonth}th`
    return `${ordinal} Week of ${monthName}`
}